<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\Middleware;
use App\Helpers\QueryHelper;
use App\Http\Controllers\backend\HelperController;
use Inertia\Inertia;

class UsersController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    protected $helper;

     public static function middleware(): array
    {
        return [
            new Middleware('can:users.view', only: ['index']),
            new Middleware('can:users.create', only: ['create', 'store']),
            new Middleware('can:users.edit', only: ['edit', 'update']),
            new Middleware('can:users.delete', only: ['destroy']),
        ];
    }
    public function __construct(HelperController $helper)
    {
        $this->helper = $helper;
    }
    public function index(Request $request, QueryHelper $queryHelper)
    {
        return $this->helper->safeGet(function () use ($request, $queryHelper) {
            $query = User::with('roles');
            $users = $queryHelper->paginateQuery(
                $query,
                $request,
                ['name', 'email', 'roles.name'],
                'name',
                'asc'
            );

            return Inertia::render('Admin/Users/Index', [
                'users' => $users,
                'filters' => $request->only(['search', 'sort', 'direction', 'perPage']),
            ]);
        });
    }

    public function create()
    {
        return $this->helper->safeGet(function () {
            $roles = Role::all();
            return Inertia::render('Admin/Users/Create',[
                'roles' => $roles,
           ]);
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);
        return $this->helper->executeWithTransaction(
            function () use ($validated) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);

                $role = Role::findOrFail($validated['role_id']);
                $user->syncRoles([$role->name]);

                return redirect()->route('users.index')
                    ->with('success', 'User created successfully');
            },
            'User created successfully',
            'User creation failed',
            $validated,
            User::class
        );
    }

    public function edit($id)
    {
        return $this->helper->safeGet(function () use ($id) {
            $user = User::with('roles')->findOrFail($id);
            $roles = Role::all();
            return Inertia::render('Admin/Users/Edit', [
                'user_x' => $user, 
                'roles' => $roles,
            ]);
        });
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'role_id' => 'required|exists:roles,id',
        ];

        $validated = $request->validate($rules);

        return $this->helper->executeWithTransaction(
            function () use ($validated, $request, $user) {
                $user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);
                $role = Role::findOrFail($validated['role_id']);
                $user->syncRoles([$role->name]);

                return redirect()->route('users.index')
                    ->with('success', 'User updated successfully');
            },
            'User updated successfully',
            'User update failed',
            $validated,
            User::class
        );
    }

    public function destroy(User $user)
    {
        return $this->helper->executeWithTransaction(
            function () use ($user) {
                if ($user->id === auth()->id()) {
                    return back()->with('error', 'You cannot delete your own account');
                }
                $user->delete();

                return redirect()->route('users.index')
                    ->with('success', 'User deleted successfully');
            },
            'User deleted successfully',
            'User deletion failed',
            ['user_id' => $user->id],
            User::class
        );
    }

    public function bulkDelete(Request $request)
    {
        $userIds = $request->input('ids', []);

        return $this->helper->executeWithTransaction(
            function () use ($userIds) {
                $users = User::whereIn('id', $userIds)->get();

                foreach ($users as $user) {
                    if ($user->id == auth()->id()) {
                        continue;
                    }
                    $user->delete();
                }

                return back()->with('success', 'Selected users deleted successfully.');
            },
            'Users deleted successfully',
            'Bulk user deletion failed',
            ['user_ids' => $userIds],
            User::class
        );
    }
    public function trash(Request $request, QueryHelper $queryHelper)
    {
        return $this->helper->safeGet(function () use ($request, $queryHelper) {
            $query = User::onlyTrashed()->with('roles');
            $users = $queryHelper->paginateQuery(
                $query,
                $request,
                ['name', 'email', 'roles.name'],
                'name',
                'asc'
            );

            return Inertia::render('Admin/Users/Trash', [
                'users' => $users,
                'filters' => $request->only(['search', 'sort', 'direction', 'perPage']),
            ]);
        });
    }

    public function forceDelete($id)
    {
        return $this->helper->executeWithTransaction(
            function () use ($id) {
                $user = User::withTrashed()->findOrFail($id);

                if ($user->id === auth()->id()) {
                    return back()->with('error', 'You cannot delete your own account');
                }

                $trashedUser = User::withTrashed()->findOrFail($user->id);
                if ($trashedUser->image_url) {
                    $this->helper->delete($trashedUser->image_url);
                }
                $trashedUser->forceDelete();

                return back()->with('success', 'User permanently deleted.');
            },
            'User permanently deleted',
            'User force deletion failed',
            ['user_id' => $id],
            User::class
        );
    }

    public function bulkForceDelete(Request $request)
    {
        $userIds = $request->input('ids', []);

        return $this->helper->executeWithTransaction(
            function () use ($userIds) {
                $users = User::withTrashed()->whereIn('id', $userIds)->get();

                foreach ($users as $user) {
                    if ($user->id == auth()->id()) {
                        continue;
                    }

                    if ($user->image_url) {
                        $this->helper->delete($user->image_url);
                    }
                    $user->forceDelete();
                }

                return back()->with('success', 'Selected users permanently deleted.');
            },
            'Users permanently deleted',
            'Bulk force deletion failed',
            ['user_ids' => $userIds],
            User::class
        );
    }

    public function restore($id)
    {
        return $this->helper->executeWithTransaction(
            function () use ($id) {
                $user = User::onlyTrashed()->findOrFail($id);
                $user->restore();

                return back()->with('success', 'User restored successfully.');
            },
            'User restored successfully',
            'User restoration failed',
            ['user_id' => $id],
            User::class
        );
    }

    public function bulkRestore(Request $request)
    {
        $userIds = $request->input('ids', []);

        return $this->helper->executeWithTransaction(
            function () use ($userIds) {
                User::onlyTrashed()
                    ->whereIn('id', $userIds)
                    ->restore();

                return back()->with('success', 'Selected users restored successfully.');
            },
            'Users restored successfully',
            'Bulk user restoration failed',
            ['user_ids' => $userIds],
            User::class
        );
    }
    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        return $this->helper->executeWithTransaction(
            function () use ($request, $user) {
                $user->update([
                    'password' => Hash::make($request->input('password')),
                ]);

                return redirect()->route('users.index')
                    ->with('success', 'Password changed successfully');
            },
            'Password changed successfully',
            'Password change failed',
            ['user_id' => $user->id],
            User::class
        );
    }
}
