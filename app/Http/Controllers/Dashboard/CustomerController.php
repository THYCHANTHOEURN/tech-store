<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\RolesEnum;
use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Customer\CustomerStoreRequest;
use App\Http\Requests\Dashboard\Customer\CustomerUpdateRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class CustomerController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the customers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAnyCustomer', User::class);

        $perPage = (int) $request->input('per_page', 10);

        $customers = QueryBuilder::for(User::role(RolesEnum::CUSTOMER->value))
            ->allowedIncludes(['roles'])
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('name', 'like', "%{$value}%")
                          ->orWhere('email', 'like', "%{$value}%")
                          ->orWhere('phone', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('status', function ($query, $value) {
                    if ($value === 'verified') {
                        $query->whereNotNull('email_verified_at');
                    } elseif ($value === 'unverified') {
                        $query->whereNull('email_verified_at');
                    }
                }),
            ])
            ->allowedSorts([
                AllowedSort::field('sort_field', 'created_at'),
                'name',
                'email',
                'created_at',
                'updated_at',
            ])
            ->defaultSort('-created_at')
            ->with('roles')
            ->paginate($perPage)
            ->appends($request->query());

        return Inertia::render('Dashboard/Customers/Index', [
            'customers' => $customers,
            'filters'   => [
                'search'    => $request->input('filter.search'),
                'status'    => $request->input('filter.status'),
                'per_page'  => $request->input('per_page', 10),
            ],
        ]);
    }

    /**
     * Show the form for creating a new customer.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->authorize('createCustomer', User::class);

        return Inertia::render('Dashboard/Customers/Create');
    }

    /**
     * Store a newly created customer in storage.
     *
     * @param \App\Http\Requests\Dashboard\Customer\CustomerStoreRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CustomerStoreRequest $request)
    {
        $this->authorize('createCustomer', User::class);

        $validated = $request->validated();

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
        $this->authorize('viewCustomer', $customer);

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
        $this->authorize('updateCustomer', $customer);

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
     * @param  \App\Http\Requests\Dashboard\Customer\CustomerUpdateRequest  $request
     * @param  \App\Models\User  $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CustomerUpdateRequest $request, User $customer)
    {
        $this->authorize('updateCustomer', $customer);

        // Check if the user has the customer role
        if (!$customer->hasRole(RolesEnum::CUSTOMER->value)) {
            return redirect()->route('dashboard.customers.index')
                ->with('error', 'User is not a customer');
        }

        $validated = $request->validated();

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
        $this->authorize('deleteCustomer', $customer);

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
        $this->authorize('exportCustomer', User::class);

        $format     = $request->input('format', 'xlsx');
        $filename   = 'customers-' . date('Y-m-d') . '.' . $format;

        return Excel::download(new CustomerExport, $filename);
    }
}
