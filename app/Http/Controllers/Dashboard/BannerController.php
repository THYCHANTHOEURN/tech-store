<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BannerController extends Controller
{
    /**
     * Display a listing of the banners.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $query = Banner::query();

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        // Handle position filter
        if ($request->filled('position') && $request->position != 'all') {
            $query->where('position', $request->position);
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

        $banners = $query->paginate(10)->appends($request->query());

        return Inertia::render('Dashboard/Banners/Index', [
            'banners' => $banners,
            'filters' => $request->only(['search', 'position', 'status']),
            'positions' => [
                ['label' => 'Slider', 'value'   => Banner::POSITION_SLIDER],
                ['label' => 'Side', 'value'     => Banner::POSITION_SIDE],
                ['label' => 'Promo', 'value'    => Banner::POSITION_PROMO],
            ]
        ]);
    }

    /**
     * Show the form for creating a new banner.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
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
}
