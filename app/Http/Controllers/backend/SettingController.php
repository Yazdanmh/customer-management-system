<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\QueryHelper;
use App\Models\Setting;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Arr;
use App\Http\Controllers\backend\HelperController;
class SettingController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    protected $helper;

    public static function middleware(): array
    {
        return [
            new Middleware('can:site_data.view', only: ['index']),
            new Middleware('can:site_data.edit', only: ['update']),
            new Middleware('can:site_data.create', only: ['update']),
            new Middleware('can:system.view_logs', only: ['logs']),
        ];
    }
    public function __construct(HelperController $helper)
    {
        $this->helper = $helper;
    }

    public function index()
    {
        return $this->helper->safeGet(function () {
            $settings = Setting::first();
            return Inertia::render('Admin/Settings/Index', [
                'settings' => $settings,
            ]);
        });
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name.en' => 'required|string',
            'site_name.fa' => 'required|string',
            'site_name.ps' => 'required|string',

            'address.en' => 'required|string',
            'address.fa' => 'required|string',
            'address.ps' => 'required|string',

            'about.en' => 'nullable|string',
            'about.fa' => 'nullable|string',
            'about.ps' => 'nullable|string',

            'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:1024',
            'site_favicon' => 'nullable|image|mimes:png,jpg,jpeg,gif,ico,webp|max:1024',

            'email' => 'required|email',
            'phone' => 'required|string',

        ]);

        return $this->helper->executeWithTransaction(
            function () use ($validated, $request) {
                $settings = Setting::first() ?? new Setting();

                if ($request->hasFile('site_logo')) {
                    $validated['site_logo'] = $this->helper->update(
                        'uploads/settings/',
                        $settings->site_logo,
                        $request->file('site_logo')
                    );
                }

                if ($request->hasFile('site_favicon')) {
                    $validated['site_favicon'] = $this->helper->update(
                        'uploads/settings/',
                        $settings->site_favicon,
                        $request->file('site_favicon')
                    );
                }

                $settings->site_name = $request->input('site_name');
                $settings->address = $request->input('address');
                $settings->about = $request->input('about');

                $settings->fill(Arr::except($validated, ['site_name', 'address', 'about']));


                $settings->save();

                return redirect()->back()->with('success', 'Settings updated successfully.');
            },
            'Settings updated successfully',
            'Settings update failed',
            $validated,
            Setting::class
        );
    }

    public function logs(Request $request, QueryHelper $helper)
    {
        return $this->helper->safeGet(function () use ($request, $helper) {
            $query = ActivityLog::with('causer');

            $logs = $helper->paginateQuery(
                $query,
                $request,
                ['log_name', 'description', 'event', 'subject_type', 'causer_type', 'batch_uuid'],
                'created_at',
                'desc'
            );

            return Inertia::render('Admin/Logs/Index', [
                'logs' => $logs,
                'filters' => $request->only(['search', 'sort', 'direction', 'perPage']),
            ]);
        });
    }

}
