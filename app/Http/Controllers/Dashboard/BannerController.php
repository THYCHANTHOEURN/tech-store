<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\BannerExport;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class BannerController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the banners.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Banner::class);

        $perPage = (int) $request->input('per_page', 10);

        $banners = QueryBuilder::for(Banner::class)
            ->allowedFilters([
                AllowedFilter::partial('search', 'title'),
                AllowedFilter::exact('position'),
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
                'title',
                'position',
                'status',
                'created_at',
                'updated_at',
            ])
            ->defaultSort('-created_at')
            ->paginate($perPage)
            ->appends($request->query());

        return Inertia::render('Dashboard/Banners/Index', [
            'banners'   => $banners,
            'filters'   => [
                'search'    => $request->input('filter.search'),
                'position'  => $request->input('filter.position'),
                'status'    => $request->input('filter.status'),
                'per_page'  => $request->input('per_page', 10),
            ],
            'positions' => [
                ['label' => 'Slider', 'value' => Banner::POSITION_SLIDER],
                ['label' => 'Side', 'value' => Banner::POSITION_SIDE],
                ['label' => 'Promo', 'value' => Banner::POSITION_PROMO],
            ],
        ]);
    }

    /**
     * Show the form for creating a new banner.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->authorize('create', Banner::class);

        return Inertia::render('Dashboard/Banners/Create', [
            'positions' => [
                ['label' => 'Slider', 'value'   => Banner::POSITION_SLIDER],
                ['label' => 'Side', 'value'     => Banner::POSITION_SIDE],
                ['label' => 'Promo', 'value'    => Banner::POSITION_PROMO],
            ]
        ]);
    }

    /**
     * Store a newly created banner in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Banner::class);

        $validated = $request->validate([
            'title'     => ['required', 'string', 'max:255'],
            'link'      => ['required', 'string', 'max:255'],
            'position'  => ['required', 'string', 'in:' . Banner::POSITION_SLIDER . ',' . Banner::POSITION_SIDE . ',' . Banner::POSITION_PROMO],
            'status'    => ['required', 'boolean'],
            'image'     => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {
            // Store the image
            $imagePath = $request->file('image')->store('banners', 'public');

            // Create the banner
            Banner::create([
                'title'     => $validated['title'],
                'image'     => $imagePath,
                'link'      => $validated['link'],
                'position'  => $validated['position'],
                'status'    => $validated['status'],
            ]);

            DB::commit();

            return redirect()->route('dashboard.banners.index')
                ->with('success', 'Banner created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating banner: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified banner.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Inertia\Response
     */
    public function show(Banner $banner)
    {
        $this->authorize('view', $banner);

        return Inertia::render('Dashboard/Banners/Show', [
            'banner'    => $banner,
            'positions' => [
                ['label' => 'Slider', 'value'   => Banner::POSITION_SLIDER],
                ['label' => 'Side', 'value'     => Banner::POSITION_SIDE],
                ['label' => 'Promo', 'value'    => Banner::POSITION_PROMO],
            ]
        ]);
    }

    /**
     * Show the form for editing the specified banner.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Inertia\Response
     */
    public function edit(Banner $banner)
    {
        $this->authorize('update', $banner);

        return Inertia::render('Dashboard/Banners/Edit', [
            'banner'    => $banner,
            'positions' => [
                ['label' => 'Slider', 'value'   => Banner::POSITION_SLIDER],
                ['label' => 'Side', 'value'     => Banner::POSITION_SIDE],
                ['label' => 'Promo', 'value'    => Banner::POSITION_PROMO],
            ]
        ]);
    }

    /**
     * Update the specified banner in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Banner $banner)
    {
        $this->authorize('update', $banner);

        $validated = $request->validate([
            'title'     => ['required', 'string', 'max:255'],
            'link'      => ['required', 'string', 'max:255'],
            'position'  => ['required', 'string', 'in:' . Banner::POSITION_SLIDER . ',' . Banner::POSITION_SIDE . ',' . Banner::POSITION_PROMO],
            'status'    => ['required', 'boolean'],
            'image'     => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {
            // Handle image update if provided
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }

                // Store the new image
                $imagePath = $request->file('image')->store('banners', 'public');
                $validated['image'] = $imagePath;
            }

            // Update the banner
            $banner->update($validated);

            DB::commit();

            return redirect()->route('dashboard.banners.index')
                ->with('success', 'Banner updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating banner: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified banner from storage.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Banner $banner)
    {
        $this->authorize('delete', $banner);

        DB::beginTransaction();

        try {
            // Delete image from storage
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }

            // Delete the banner
            $banner->delete();

            DB::commit();

            return redirect()->route('dashboard.banners.index')
                ->with('success', 'Banner deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting banner: ' . $e->getMessage());
        }
    }

    /**
     * Export banners to Excel or CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $this->authorize('export', Banner::class);

        $format     = $request->input('format', 'xlsx');
        $filename   = 'banners-' . date('Y-m-d') . '.' . $format;

        return Excel::download(new BannerExport, $filename);
    }
}
