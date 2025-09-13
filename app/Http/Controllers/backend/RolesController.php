<?php

namespace App\Http\Controllers\backend;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
class RolesController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    protected $helper;

    public static function middleware(): array
    {
        return [
            new Middleware('can:roles.view', only: ['index']),
            new Middleware('can:roles.create', only: ['create', 'store']),
            new Middleware('can:roles.edit', only: ['edit', 'update']),
            new Middleware('can:roles.delete', only: ['destroy']),
        ];
    }

    public function __construct(HelperController $helper)
    {
        $this->helper = $helper;
    }
    public function index(Request $request, QueryHelper $queryHelper)
    {
        return $this->helper->safeGet(function () use ($request, $queryHelper) {
            $query = Role::with('permissions');

            $sortColumn = $request->input('sort', 'name');
            $sortDirection = $request->input('direction', 'asc');

            $roles = $queryHelper->paginateQuery(
                $query,
                $request,
                ['name'],
                $sortColumn,
                $sortDirection
                
            );

            return Inertia::render('Admin/Roles/Index', [
                'roles' => $roles,
                'filters' => $request->only(['search', 'sort', 'direction', 'perPage']),
            ]);
        });
    }

    public function create()
    {
        return $this->helper->safeGet(function () {
            return Inertia::render('Admin/Roles/Create');
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);
        return $this->helper->executeWithTransaction(
            function () use ($validated) {
                $role = Role::create([
                    'name' => $validated['name'],
                    'guard_name' => 'web',
                ]);
                $role->syncPermissions($validated['permissions']);
                return redirect()->route('roles.index')->with('success', 'Role created successfully');
            },
            'Role created successfully',
            'Role creation failed',
            $validated,
            Role::class
        );
    }

    public function edit(Role $role)
    {
        return $this->helper->safeGet(function () use ($role) {
            return Inertia::render('Admin/Roles/Edit', [
                'role' => $role,
                'assignedPermissions' => $role->permissions->pluck('name'),
            ]);
        });
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        return $this->helper->executeWithTransaction(
            function () use ($validated, $role) {
                $role->update(['name' => $validated['name']]);
                $role->syncPermissions($validated['permissions']);
                return redirect()->route('roles.index')->with('success', 'Role updated successfully');
            },
            'Role updated successfully',
            'Role update failed',
            $validated,
            $role
        );
    }

    public function destroy(Role $role)
    {
        return $this->helper->executeWithTransaction(
            function () use ($role) {
                $role->delete();
                return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
            },
            'Role deleted successfully',
            'Role deletion failed',
            ['role_id' => $role->id],
            $role
        );
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        return $this->helper->executeWithTransaction(function () use ($ids) {
            Role::whereIn('id', $ids)->delete();
            return redirect()->route('roles.index')->with('success', 'Selected roles moved to trash.');
        }, 'Bulk delete successful', 'Bulk delete failed', ['role_ids' => $ids], Role::class);
    }

    public function trash(Request $request, QueryHelper $queryHelper)
    {
        return $this->helper->safeGet(function () use ($request, $queryHelper) {
            $query = Role::onlyTrashed()->with('permissions');

            $sortColumn = $request->input('sort', 'name');
            $sortDirection = $request->input('direction', 'asc');

            $roles = $queryHelper->paginateQuery(
                $query,
                $request,
                ['name'],
                $sortColumn,
                $sortDirection
            );

            return Inertia::render('Admin/Roles/Trash', [
                'roles' => $roles,
                'filters' => $request->only(['search', 'sort', 'direction', 'perPage']),
            ]);
        });
    }

    public function restore($id)
    {
        return $this->helper->executeWithTransaction(function () use ($id) {
            $role = Role::onlyTrashed()->findOrFail($id);
            $role->restore();
            return redirect()->route('roles.trash')->with('success', 'Role restored successfully.');
        }, 'Role restored successfully', 'Role restoration failed', ['role_id' => $id], Role::class);
    }

    public function forceDelete($id)
    {
        return $this->helper->executeWithTransaction(function () use ($id) {
            $role = Role::onlyTrashed()->findOrFail($id);
            $role->forceDelete();
            return redirect()->route('roles.trash')->with('success', 'Role permanently deleted.');
        }, 'Role permanently deleted', 'Permanent deletion failed', ['role_id' => $id], Role::class);
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        return $this->helper->executeWithTransaction(function () use ($ids) {
            Role::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            return redirect()->route('roles.trash')->with('success', 'Selected roles permanently deleted.');
        }, 'Bulk force delete successful', 'Bulk force delete failed', ['role_ids' => $ids], Role::class);
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);

        return $this->helper->executeWithTransaction(function () use ($ids) {
            Role::onlyTrashed()->whereIn('id', $ids)->restore();
            return redirect()->route('roles.trash')->with('success', 'Selected roles have been restored.');
        }, 'Bulk restore successful', 'Bulk restore failed', ['role_ids' => $ids], Role::class);
    }
}
