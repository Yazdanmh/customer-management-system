<?php

namespace App\Http\Controllers\backend;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\backend\HelperController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
    use App\Exports\CustomersExport;
    use Maatwebsite\Excel\Facades\Excel;

class CustomersController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    protected $helper;

    public function __construct(HelperController $helper)
    {
        $this->helper = $helper;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('can:customers.view', only: ['index', 'trash']),
            new Middleware('can:customers.create', only: ['create', 'store']),
            new Middleware('can:customers.edit', only: ['edit', 'update']),
            new Middleware('can:customers.delete', only: [
                'destroy',
                'bulkDelete',
                'restore',
                'forceDelete',
                'bulkForceDelete',
                'bulkRestore'
            ]),
        ];
    }

    public function index(Request $request, QueryHelper $queryHelper)
    {
        return $this->helper->safeGet(function () use ($request, $queryHelper) {
            $query = Customer::query();

            $customers = $queryHelper->paginateQuery(
                $query,
                $request,
                ['name', 'username', 'status'],
                'id',
                'desc'
            );

            return Inertia::render('Admin/Customers/Index', [
                'customers' => $customers,
                'filters' => $request->only(['search', 'sort', 'direction', 'perPage']),
            ]);
        });
    }


    public function create()
    {
        return $this->helper->safeGet(function () {
            return Inertia::render('Admin/Customers/Create');
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:customers,username',
            'password'  => 'required|string|max:255',
            'link'      => 'nullable|url|max:255',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:800',
            'status'    => 'required|boolean',
        ]);
        return $this->helper->executeWithTransaction(function () use ($validated, $request) {
            $imagePath = $request->hasFile('image_url')
                ? $this->helper->upload('customers/', $request->file('image_url'))
                : null;

            Customer::create([
                'name'      => $validated['name'],
                'username'  => $validated['username'],
                'password'  => $validated['password'],
                'link'      => $validated['link'] ?? null,
                'image_url' => $imagePath,
                'status'    => $validated['status'],
            ]);

            return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
        }, 'Customer created successfully', 'Customer creation failed', $validated, Customer::class);
    }

    public function show($id)
    {
        return $this->helper->safeGet(function () use ($id) {
            $customer = Customer::findOrFail($id);
            return Inertia::render('Admin/Customers/Details', [
                'customer' => $customer,
            ]);
        });
    }
    public function edit($id)
    {
        return $this->helper->safeGet(function () use ($id) {
            $customer = Customer::findOrFail($id);
            return Inertia::render('Admin/Customers/Edit', [
                'customer' => $customer,
            ]);
        });
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'      => 'nullable|string|max:255',
            'username'  => 'nullable|string|max:255|unique:customers,username,' . $id,
            'password'  => 'nullable|string|max:255',
            'link'      => 'nullable|url|max:255',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:800',
            'status'    => 'nullable|boolean',
        ]);

        return $this->helper->executeWithTransaction(function () use ($validated, $request, $id) {
            $customer = Customer::findOrFail($id);

            if ($request->hasFile('image_url')) {
                $customer->image_url = $this->helper->update('customers/', $customer->image_url, $request->file('image_url'));
            }

            foreach (['name', 'username', 'password', 'link', 'status'] as $field) {
                if (isset($validated[$field])) {
                    $customer->$field = $validated[$field];
                }
            }

            $customer->save();

            return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
        }, 'Customer updated successfully', 'Customer update failed', $validated, Customer::class);
    }

    public function destroy($id)
    {
        return $this->helper->executeWithTransaction(function () use ($id) {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
        }, 'Customer deleted successfully', 'Customer deletion failed', ['customer_id' => $id], Customer::class);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        return $this->helper->executeWithTransaction(function () use ($ids) {
            Customer::whereIn('id', $ids)->delete();
            return redirect()->route('customers.index')->with('success', 'Selected customers moved to trash.');
        }, 'Bulk delete successful', 'Bulk delete failed', ['customer_ids' => $ids], Customer::class);
    }

    public function trash(Request $request, QueryHelper $queryHelper)
    {
        return $this->helper->safeGet(function () use ($request, $queryHelper) {
            $query = Customer::onlyTrashed();

            $customers = $queryHelper->paginateQuery(
                $query,
                $request,
                ['name', 'username', 'status'],
                'created_at',
                'desc'
            );

            return Inertia::render('Admin/Customers/Trash', [
                'customers'    => $customers,
                'filters'      => $request->only(['search', 'sort', 'direction', 'perPage']),
                'showTrashed'  => true,
            ]);
        });
    }

    public function restore($id)
    {
        return $this->helper->executeWithTransaction(function () use ($id) {
            $customer = Customer::onlyTrashed()->findOrFail($id);
            $customer->restore();
            return redirect()->route('customers.trash')->with('success', 'Customer restored successfully.');
        }, 'Customer restored successfully', 'Customer restoration failed', ['customer_id' => $id], Customer::class);
    }

    public function forceDelete($id)
    {
        return $this->helper->executeWithTransaction(function () use ($id) {
            $customer = Customer::onlyTrashed()->findOrFail($id);
            if ($customer->image_url) {
                $this->helper->delete($customer->image_url);
            }
            $customer->forceDelete();
            return redirect()->route('customers.trash')->with('success', 'Customer permanently deleted.');
        }, 'Customer permanently deleted', 'Permanent deletion failed', ['customer_id' => $id], Customer::class);
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        return $this->helper->executeWithTransaction(function () use ($ids) {
            $customers = Customer::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($customers as $customer) {
                if ($customer->image_url) {
                    $this->helper->delete($customer->image_url);
                }
            }
            Customer::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            return redirect()->route('customers.trash')->with('success', 'Selected customers permanently deleted.');
        }, 'Bulk customer deletion successful', 'Bulk customer deletion failed', ['customer_ids' => $ids], Customer::class);
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        return $this->helper->executeWithTransaction(function () use ($ids) {
            Customer::onlyTrashed()->whereIn('id', $ids)->restore();
            return redirect()->route('customers.trash')->with('success', 'Selected customers have been restored.');
        }, 'Bulk customer restoration successful', 'Bulk customer restoration failed', ['customer_ids' => $ids], Customer::class);
    }
    public function toggleStatus($id)
    {
        $customer = Customer::findOrFail($id);

        return $this->helper->executeWithTransaction(
            function () use ($customer) {
                $customer->status = !$customer->status;
                $customer->save();

                return redirect()
                    ->route('customers.index')
                    ->with('success', 'Customer status toggled successfully.');
            },
            'Customer status toggled successfully',
            'Toggling customer status failed',
            ['customer_id' => $customer->id],
            Customer::class
        );
    }



    public function export()
    {
        return Excel::download(new CustomersExport, 'customers.xlsx');
    }
}
