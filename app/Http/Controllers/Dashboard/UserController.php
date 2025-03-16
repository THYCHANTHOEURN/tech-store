<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // Exclude customers from this view as they are managed in CustomerController
        $query = User::query()
            ->with('roles')
            ->whereHas('roles', function($q) {
                $q->where('name', '!=', RolesEnum::CUSTOMER->value);
            });

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Handle role filter
        if ($request->filled('role') && $request->role != 'all') {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Handle status filter
        if ($request->filled('status') && $request->status != 'all') {
            switch ($request->status) {
                case 'verified':
                    $query->whereNotNull('email_verified_at');
                    break;
                case 'unverified':
                    $query->whereNull('email_verified_at');
                    break;
            }
        }

        // Handle sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $users = $query->paginate(10)->appends($request->query());
        $roles = Role::where('name', '!=', RolesEnum::CUSTOMER->value)->get();

        return Inertia::render('Dashboard/Users/Index', [
            'users'     => $users,
            'roles'     => $roles,
            'filters'   => $request->only(['search', 'role', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        // Get all roles except customer
        $roles = Role::where('name', '!=', RolesEnum::CUSTOMER->value)->get();

        return Inertia::render('Dashboard/Users/Create', [
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'  => ['required', 'string', 'min:8'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'address'   => ['nullable', 'string'],
            'role'      => ['required', 'exists:roles,name'],
        ]);

        DB::beginTransaction();

        try {
            // Create the user
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'phone'     => $validated['phone'] ?? null,
                'address'   => $validated['address'] ?? null,
            ]);

            // Assign role
            $user->assignRole($validated['role']);

            DB::commit();

            return redirect()->route('dashboard.users.index')
                ->with('success', 'User created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Inertia\Response | \Illuminate\Http\RedirectResponse
     */
    public function show(User $user)
    {
        // Make sure the user is not a customer
        if ($user->hasRole(RolesEnum::CUSTOMER->value)) {
            return redirect()->route('dashboard.users.index')
                ->with('error', 'Please use customer management for this user.');
        }

        $user->load(['roles']);

        return Inertia::render('Dashboard/Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Inertia\Response | \Illuminate\Http\RedirectResponse
     */
    public function edit(User $user)
    {
        // Make sure the user is not a customer
        if ($user->hasRole(RolesEnum::CUSTOMER->value)) {
            return redirect()->route('dashboard.users.index')
                ->with('error', 'Please use customer management for this user.');
        }

        $roles = Role::where('name', '!=', RolesEnum::CUSTOMER->value)->get();
        $user->load('roles');

        return Inertia::render('Dashboard/Users/Edit', [
            'user'      => $user,
            'userRole'  => $user->roles->first()->name ?? null,
            'roles'     => $roles,
        ]);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Make sure the user is not a customer
        if ($user->hasRole(RolesEnum::CUSTOMER->value)) {
            return redirect()->route('dashboard.users.index')
                ->with('error', 'Please use customer management for this user.');
        }

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'  => ['nullable', 'string', 'min:8'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'address'   => ['nullable', 'string'],
            'role'      => ['required', 'exists:roles,name'],
        ]);

        DB::beginTransaction();

        try {
            // Update user data
            $userData = [
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'phone'     => $validated['phone'] ?? null,
                'address'   => $validated['address'] ?? null,
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);

            // Update roles (remove existing and add new)
            $user->syncRoles([$validated['role']]);

            DB::commit();

            return redirect()->route('dashboard.users.index')
                ->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Make sure the user is not a customer
        if ($user->hasRole(RolesEnum::CUSTOMER->value)) {
            return redirect()->route('dashboard.users.index')
                ->with('error', 'Please use customer management for this user.');
        }

        // Prevent deleting your own account
        if ($user->id === auth()->id()) {
            return redirect()->route('dashboard.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        DB::beginTransaction();

        try {
            // Delete the user
            $user->delete();

            DB::commit();

            return redirect()->route('dashboard.users.index')
                ->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard.users.index')
                ->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
}
