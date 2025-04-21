<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\RolesEnum;
use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $query = User::role(RolesEnum::CUSTOMER->value)->with('roles');

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
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

        $customers = $query->paginate(10)->appends($request->query());

        return Inertia::render('Dashboard/Customers/Index', [
            'customers' => $customers,
            'filters'   => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new customer.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Dashboard/Customers/Create');
    }

    /**
     * Store a newly created customer in storage.
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
        ]);

        DB::beginTransaction();

        try {
            // Create the customer
            $customer = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'phone'     => $validated['phone'] ?? null,
                'address'   => $validated['address'] ?? null,
            ]);

            // Assign customer role
            $customer->assignRole(RolesEnum::CUSTOMER->value);

            DB::commit();

            return redirect()->route('dashboard.customers.index')
                ->with('success', 'Customer created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating customer: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified customer.
     *
     * @param  \App\Models\User  $customer
     * @return \Inertia\Response | \Illuminate\Http\RedirectResponse
     */
    public function show(User $customer)
    {
        // Check if the user has the customer role
        if (!$customer->hasRole(RolesEnum::CUSTOMER->value)) {
            return redirect()->route('dashboard.customers.index')
                ->with('error', 'User is not a customer');
        }

        // Load customer with orders
        $customer->load(['orders' => function($query) {
            $query->latest()->take(5);
        }]);

        // Get customer stats
        $stats = [
            'total_orders'      => $customer->orders()->count(),
            'total_spent'       => $customer->orders()->where('status', 'completed')->sum('total_amount'),
            'wishlist_items'    => $customer->wishlistItems()->count(),
            'cart_items'        => $customer->cartItems()->count(),
        ];

        return Inertia::render('Dashboard/Customers/Show', [
            'customer' => $customer,
            'stats' => $stats
        ]);
    }

    /**
     * Show the form for editing the specified customer.
     *
     * @param  \App\Models\User  $customer
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function edit(User $customer)
    {
        // Check if the user has the customer role
        if (!$customer->hasRole(RolesEnum::CUSTOMER->value)) {
            return redirect()->route('dashboard.customers.index')
                ->with('error', 'User is not a customer');
        }

        return Inertia::render('Dashboard/Customers/Edit', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $customer)
    {
        // Check if the user has the customer role
        if (!$customer->hasRole(RolesEnum::CUSTOMER->value)) {
            return redirect()->route('dashboard.customers.index')
                ->with('error', 'User is not a customer');
        }

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($customer->id)],
            'password'  => ['nullable', 'string', 'min:8'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'address'   => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            // Update customer data
            $customerData = [
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'phone'     => $validated['phone'] ?? null,
                'address'   => $validated['address'] ?? null,
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $customerData['password'] = Hash::make($validated['password']);
            }

            $customer->update($customerData);

            DB::commit();

            return redirect()->route('dashboard.customers.index')
                ->with('success', 'Customer updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating customer: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified customer from storage.
     *
     * @param  \App\Models\User  $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $customer)
    {
        // Check if the user has the customer role
        if (!$customer->hasRole(RolesEnum::CUSTOMER->value)) {
            return redirect()->route('dashboard.customers.index')
                ->with('error', 'User is not a customer');
        }

        DB::beginTransaction();

        try {
            // Delete the customer
            $customer->delete();

            DB::commit();

            return redirect()->route('dashboard.customers.index')
                ->with('success', 'Customer deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard.customers.index')
                ->with('error', 'Error deleting customer: ' . $e->getMessage());
        }
    }

    /**
     * Export customers to Excel or CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $format     = $request->format ?? 'xlsx';
        $filename   = 'customers-' . date('Y-m-d') . '.' . $format;

        return Excel::download(new CustomerExport, $filename);
    }
}
