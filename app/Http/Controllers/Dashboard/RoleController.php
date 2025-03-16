<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $query = Role::query()->with('permissions');

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Handle sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $roles = $query->paginate(10)->appends($request->query());

        return Inertia::render('Dashboard/Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new role.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $permissions = Permission::all();

        return Inertia::render('Dashboard/Roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'permissions' => ['nullable', 'array'],
        ]);

        DB::beginTransaction();

        try {
            // Create the role
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web',
            ]);

            // Assign permissions
            if (!empty($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }

            DB::commit();

            return redirect()->route('dashboard.roles.index')
                ->with('success', 'Role created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified role.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Inertia\Response
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        $users = $role->users()->paginate(10);

        return Inertia::render('Dashboard/Roles/Show', [
            'role' => $role,
            'users' => $users,
        ]);
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Inertia\Response
     */
    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all();

        return Inertia::render('Dashboard/Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $role->permissions->pluck('id')->toArray(),
        ]);
    }

    /**
     * Update the specified role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'permissions' => ['nullable', 'array'],
        ]);

        DB::beginTransaction();

        try {
            // Update role name
            $role->update([
                'name' => $validated['name'],
            ]);

            // Sync permissions
            if (isset($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            } else {
                $role->permissions()->detach();
            }

            DB::commit();

            return redirect()->route('dashboard.roles.index')
                ->with('success', 'Role updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role from storage.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role)
    {
        DB::beginTransaction();

        try {
            // Check if users are assigned to this role
            if ($role->users()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete role with assigned users. Reassign users first.');
            }

            // Delete the role
            $role->delete();

            DB::commit();

            return redirect()->route('dashboard.roles.index')
                ->with('success', 'Role deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }
}
