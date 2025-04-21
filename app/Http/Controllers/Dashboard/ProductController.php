<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Exports\ProductsExport;
use App\Exports\ProductsTemplateExport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'primaryImage']);

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Handle filters
        if ($request->filled('category') && $request->category != 'null') {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('brand') && $request->brand != 'null') {
            $query->where('brand_id', $request->brand);
        }

        if ($request->filled('status') && $request->status != 'all') {
            switch ($request->status) {
                case 'published':
                    $query->where('status', true);
                    break;
                case 'unpublished':
                    $query->where('status', false);
                    break;
            }
        }

        if ($request->filled('featured') && $request->featured != 'all') {
            switch ($request->featured) {
                case 'featured':
                    $query->where('featured', operator: true);
                    break;
                case 'not-featured':
                    $query->where('featured', false);
                    break;
            }
        }

        // Handle sorting
        $sortField  = $request->input('sort_field', 'created_at');
        $sortOrder  = $request->input('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $products   = $query->paginate(10)->appends($request->query());

        // Get all categories and brands for filters
        $categories = Category::all();
        $brands     = Brand::all();

        return Inertia::render('Dashboard/Products/Index', [
            'products'      => $products,
            'filters'       => $request->only(['search', 'category', 'brand', 'status', 'featured']),
            'categories'    => $categories,
            'brands'        => $brands,
        ]);
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $categories = Category::where('status', true)->get();
        $brands     = Brand::where('status', true)->get();

        return Inertia::render('Dashboard/Products/Create', [
            'categories'    => $categories,
            'brands'        => $brands,
        ]);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'brand_id'      => 'required|exists:brands,id',
            'price'         => 'required|numeric|min:0',
            'sale_price'    => 'nullable|numeric|min:0|lt:price',
            'stock'         => 'required|integer|min:0',
            'description'   => 'nullable|string',
            'status'        => 'required|boolean',
            'featured'      => 'required|boolean',
            'images'        => 'sometimes|array',
            'images.*'      => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Generate slug
        $slug           = Str::slug($validated['name']);
        $count          = 1;
        $originalSlug   = $slug;

        // Check if slug exists
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Generate SKU
        $sku = strtoupper(substr(str_replace(['-', ' '], '', $slug), 0, 8) . '-' . uniqid());

        // 2. Start database transaction
        DB::beginTransaction();

        try {
            // 3. Create the product
            $product = Product::create([
                'name'          => $validated['name'],
                'slug'          => $slug,
                'sku'           => $sku,
                'category_id'   => $validated['category_id'],
                'brand_id'      => $validated['brand_id'],
                'price'         => $validated['price'],
                'sale_price'    => $validated['sale_price'] ?? null,
                'stock'         => $validated['stock'],
                'description'   => $validated['description'] ?? null,
                'status'        => $validated['status'],
                'featured'      => $validated['featured'],
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $isPrimary = true;
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');

                    ProductImage::create([
                        'product_id'    => $product->id,
                        'image'         => $path,
                        'is_primary'    => $isPrimary,
                    ]);

                    $isPrimary = false;
                }
            } else {
                // Create a default image if no images are uploaded
                ProductImage::create([
                    'product_id'    => $product->id,
                    'image'         => 'products/default.jpg',
                    'is_primary'    => true,
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard.products.index')
                ->with('success', 'Product created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Inertia\Response
     */
    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'productImages', 'reviews.user']);

        return Inertia::render('Dashboard/Products/Show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Inertia\Response
     */
    public function edit(Product $product)
    {
        $product->load(['productImages']);
        $categories = Category::all();
        $brands     = Brand::all();

        return Inertia::render('Dashboard/Products/Edit', [
            'product'       => $product,
            'categories'    => $categories,
            'brands'        => $brands,
        ]);
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'required|exists:brands,id',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0|lt:price',
            'stock'             => 'required|integer|min:0',
            'description'       => 'nullable|string',
            'status'            => 'required|boolean',
            'featured'          => 'required|boolean',
            'images'            => 'sometimes|array',
            'images.*'          => 'image|mimes:jpeg,png,jpg|max:2048',
            'remove_images'     => 'sometimes|array',
            'remove_images.*'   => 'exists:product_images,id',
            'primary_image'     => 'sometimes|nullable|exists:product_images,id',
        ]);

        DB::beginTransaction();

        try {
            // Update product
            $product->update([
                'name'          => $validated['name'],
                'category_id'   => $validated['category_id'],
                'brand_id'      => $validated['brand_id'],
                'price'         => $validated['price'],
                'sale_price'    => $validated['sale_price'] ?? null,
                'stock'         => $validated['stock'],
                'description'   => $validated['description'] ?? null,
                'status'        => $validated['status'],
                'featured'      => $validated['featured'],
            ]);

            // Handle removed images
            if ($request->has('remove_images') && is_array($request->remove_images)) {
                foreach ($request->remove_images as $imageId) {
                    $image = ProductImage::find($imageId);
                    if ($image) {
                        // Remove from storage
                        if (Storage::disk('public')->exists($image->image) && $image->image != 'products/default.jpg') {
                            Storage::disk('public')->delete($image->image);
                        }
                        $image->delete();
                    }
                }
            }

            // Handle primary image change
            if ($request->has('primary_image')) {
                // Reset all images to non-primary
                ProductImage::where('product_id', $product->id)
                    ->update(['is_primary' => false]);

                // Set the new primary image
                if ($request->primary_image) {
                    ProductImage::where('id', $request->primary_image)
                        ->update(['is_primary' => true]);
                }
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $hasExistingImages = $product->productImages()->exists();

                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');

                    ProductImage::create([
                        'product_id'    => $product->id,
                        'image'         => $path,
                        'is_primary'    => !$hasExistingImages && !$request->has('primary_image'),
                    ]);

                    $hasExistingImages = true;
                }
            }

            // Ensure at least one image exists
            if (!$product->productImages()->exists()) {
                ProductImage::create([
                    'product_id'    => $product->id,
                    'image'         => 'products/default.jpg',
                    'is_primary'    => true,
                ]);
            }

            // Ensure there is a primary image
            if (!$product->productImages()->where('is_primary', true)->exists()) {
                $firstImage = $product->productImages()->first();
                if ($firstImage) {
                    $firstImage->update(['is_primary' => true]);
                }
            }

            DB::commit();

            return redirect()->route('dashboard.products.index')
                ->with('success', 'Product updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {

        DB::beginTransaction();

        try {
            // Delete associated images from storage
            foreach ($product->productImages as $image) {
                if (Storage::disk('public')->exists($image->image)) {
                    Storage::disk('public')->delete($image->image);
                }
            }

            $product->delete();

            DB::commit();

            return redirect()->route('dashboard.products.index')
                ->with('success', 'Product deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard.products.index')
                ->with('error', 'Error deleting product: ' . $e->getMessage());
        }
    }

    /**
     * Export products to Excel or CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $format     = $request->format ?? 'xlsx';
        $filename   = 'products-' . date('Y-m-d') . '.' . $format;

        return Excel::download(new ProductsExport, $filename);
    }

    /**
     * Import products from Excel or CSV
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            DB::beginTransaction();

            Excel::import(new ProductsImport, $request->file('file'));

            DB::commit();

            return redirect()->route('dashboard.products.index')
                ->with('success', 'Products imported successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error importing products: ' . $e->getMessage());
        }
    }

    /**
     * Download product template for import
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function template(Request $request)
    {
        $format     = $request->format ?? 'xlsx';
        $filename   = 'product-template.' . $format;

        return Excel::download(new ProductsTemplateExport, $filename);
    }
}
