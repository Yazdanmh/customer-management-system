<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Models\SiteSetting;  // Import your SiteSetting model
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        // Get the first or default site setting
        $setting = Setting::first();

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $request->user(),
            ],

            // Share site setting data globally
            'setting' => $setting ? $setting : null,
        ];
    }
}
