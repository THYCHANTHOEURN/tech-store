<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Exports\BrandExport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;

class BrandController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the brands.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Brand::class);

        $query = Brand::query();

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Handle status filter
        if ($request->filled('status') && $request->status != 'all') {
            switch ($request->status) {
                case 'active':
                    $query->where('status', true);
                    break;
                case 'inactive':
                    $query->where('status', false);
                    break;
            }
        }

        // Handle sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $perPage = (int) $request->input('per_page', 10);
        $brands = $query->paginate($perPage)->appends($request->query());

        return Inertia::render('Dashboard/Brands/Index', [
            'brands'    => $brands,
            'filters'   => $request->only(['search', 'status', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new brand.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->authorize('create', Brand::class);

        return Inertia::render('Dashboard/Brands/Create');
    }

    /**
     * Store a newly created brand in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Brand::class);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'status'        => ['required', 'boolean'],
            'logo'          => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // Generate slug
        $slug           = Str::slug($validated['name']);
        $count          = 1;
        $originalSlug   = $slug;

        // Check if slug exists
        while (Brand::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        DB::beginTransaction();

        try {
            // Store the logo
            $logoPath = $request->file('logo')->store('brands', 'public');

            // Create the brand
            Brand::create([
                'name'          => $validated['name'],
                'slug'          => $slug,
                'logo'          => $logoPath,
                'description'   => $validated['description'] ?? null,
                'status'        => $validated['status'],
            ]);

            DB::commit();

            return redirect()->route('dashboard.brands.index')
                ->with('success', 'Brand created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating brand: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified brand.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Inertia\Response
     */
    public function show(Brand $brand)
    {
        $this->authorize('view', $brand);

        // Get products belonging to this brand with pagination
        $products = $brand->products()->with(['category', 'primaryImage'])->paginate(10);

        return Inertia::render('Dashboard/Brands/Show', [
            'brand'     => $brand,
            'products'  => $products,
        ]);
    }

    /**
     * Show the form for editing the specified brand.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Inertia\Response
     */
    public function edit(Brand $brand)
    {
        $this->authorize('update', $brand);

        return Inertia::render('Dashboard/Brands/Edit', [
            'brand' => $brand,
        ]);
    }

    /**
     * Update the specified brand in storage.
     *
     * @param  Request  $request
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Brand $brand)
    {
        $this->authorize('update', $brand);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'status'        => ['required', 'boolean'],
            'logo'          => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {
            // Handle logo update if provided
            if ($request->hasFile('logo')) {
                // Delete old logo if exists and not a default logo
                if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                    Storage::disk('public')->delete($brand->logo);
                }

                // Store the new logo
                $logoPath = $request->file('logo')->store('brands', 'public');
                $validated['logo'] = $logoPath;
            }

            // Update the brand
            $brand->update($validated);

            DB::commit();

            return redirect()->route('dashboard.brands.index')
                ->with('success', 'Brand updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating brand: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified brand from storage.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Brand $brand)
    {
        $this->authorize('delete', $brand);

        DB::beginTransaction();

        try {
            // Check if brand has products
            if ($brand->products()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete brand with products. Move or delete the products first.');
            }

            // Delete logo from storage
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }

            // Delete the brand
            $brand->delete();

            DB::commit();

            return redirect()->route('dashboard.brands.index')
                ->with('success', 'Brand deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting brand: ' . $e->getMessage());
        }
    }

    /**
     * Export brands to Excel or CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $this->authorize('export', Brand::class);
        
        $format     = $request->input('format', 'xlsx');
        $filename   = 'brands-' . date('Y-m-d') . '.' . $format;

        return Excel::download(new BrandExport, $filename);
    }
}
