<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Exports\CategoryExport;
use App\Http\Requests\Dashboard\Category\CategoryStoreRequest;
use App\Http\Requests\Dashboard\Category\CategoryUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\AllowedInclude;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the categories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        $perPage = (int) $request->input('per_page', 10);

        $categories = QueryBuilder::for(Category::class)
            ->allowedIncludes(['parent', 'children'])
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('name', 'like', "%{$value}%")
                          ->orWhere('description', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('parent', function ($query, $value) {
                    if ($value === 'root') {
                        $query->whereNull('parent_id');
                    } else {
                        $query->where('parent_id', $value);
                    }
                }),
                AllowedFilter::callback('status', function ($query, $value) {
                    if ($value === 'active') {
                        $query->where('status', true);
                    } elseif ($value === 'inactive') {
                        $query->where('status', false);
                    }
                }),
            ])
            ->allowedSorts([
                AllowedSort::field('sort_field', 'created_at'),
                'name',
                'status',
                'created_at',
                'updated_at',
            ])
            ->defaultSort('-created_at')
            ->with('parent')
            ->paginate($perPage)
            ->appends($request->query());

        // Get parent categories for filter dropdown
        $parentCategories = Category::whereNull('parent_id')->get();

        return Inertia::render('Dashboard/Categories/Index', [
            'categories'        => $categories,
            'filters'           => [
                'search'    => $request->input('filter.search'),
                'parent'    => $request->input('filter.parent'),
                'status'    => $request->input('filter.status'),
                'per_page'  => $request->input('per_page', 10),
            ],
            'parentCategories'  => $parentCategories,
        ]);
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->authorize('create', Category::class);

        $parentCategories = Category::whereNull('parent_id')->where('status', true)->get();

        return Inertia::render('Dashboard/Categories/Create', [
            'parentCategories' => $parentCategories,
        ]);
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \App\Http\Requests\Dashboard\Category\CategoryStoreRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryStoreRequest $request)
    {
        $this->authorize('create', Category::class);

        $validated = $request->validated();

        // Generate slug
        $slug           = Str::slug($validated['name']);
        $count          = 1;
        $originalSlug   = $slug;

        // Check if slug exists
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        DB::beginTransaction();

        try {
            // Store the image
            $imagePath = $request->file('image')->store('categories', 'public');

            // Create the category
            Category::create([
                'name'          => $validated['name'],
                'slug'          => $slug,
                'parent_id'     => $validated['parent_id'] ?? null,
                'image'         => $imagePath,
                'description'   => $validated['description'] ?? null,
                'status'        => $validated['status'],
            ]);

            DB::commit();

            return redirect()->route('dashboard.categories.index')
                ->with('success', 'Category created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Inertia\Response
     */
    public function show(Category $category)
    {
        $this->authorize('view', $category);

        $category->load(['parent', 'children']);

        // Get products belonging to this category
        $products = $category->products()->with(['brand', 'primaryImage'])->paginate(10);

        return Inertia::render('Dashboard/Categories/Show', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Inertia\Response
     */
    public function edit(Category $category)
    {
        $this->authorize('update', $category);

        $parentCategories = Category::whereNull('parent_id')->where('status', true)->get();

        return Inertia::render('Dashboard/Categories/Edit', [
            'category'          => $category,
            'parentCategories'  => $parentCategories,
        ]);
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \App\Http\Requests\Dashboard\Category\CategoryUpdateRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validated();

        // Make sure category is not set as its own parent
        if ($validated['parent_id'] == $category->id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A category cannot be its own parent.');
        }

        DB::beginTransaction();

        try {
            // Handle image update if provided
            if ($request->hasFile('image')) {
                // Delete old image if exists and not a default image
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }

                // Store the new image
                $imagePath = $request->file('image')->store('categories', 'public');
                $validated['image'] = $imagePath;
            }

            // Update the category
            $category->update($validated);

            DB::commit();

            return redirect()->route('dashboard.categories.index')
                ->with('success', 'Category updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        DB::beginTransaction();

        try {
            // Check if category has products
            if ($category->products()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete category with products. Move or delete the products first.');
            }

            // Check if category has children categories
            if ($category->children()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete category with sub-categories. Delete the sub-categories first.');
            }

            // Delete image from storage
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            // Delete the category
            $category->delete();

            DB::commit();

            return redirect()->route('dashboard.categories.index')
                ->with('success', 'Category deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }

    /**
     * Export categories to Excel or CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $this->authorize('export', Category::class);

        $format     = $request->input('format', 'xlsx');
        $filename   = 'categories-' . date('Y-m-d') . '.' . $format;

        return Excel::download(new CategoryExport, $filename);
    }
}
