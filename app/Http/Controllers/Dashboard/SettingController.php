<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $group = $request->query('group', 'general');

        $settings = Setting::where('group', $group)->get()->map(function ($setting) {
            $data = $setting->toArray();
            if ($setting->type === 'image') {
                $data['image_url'] = $setting->image_url;
            }
            return $data;
        });

        $groups = Setting::select('group')->distinct()->pluck('group');

        return Inertia::render('Dashboard/Settings/Index', [
            'settings' => $settings,
            'groups' => $groups,
            'currentGroup' => $group
        ]);
    }

    /**
     * Update the specified settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:settings,id',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'required|string'
        ]);

        DB::beginTransaction();

        try {
            foreach ($data['settings'] as $item) {
                $setting = Setting::find($item['id']);

                // Handle image uploads
                if ($setting->type === 'image' && $request->hasFile("files.{$setting->key}")) {
                    $file = $request->file("files.{$setting->key}");

                    // Delete old file if exists
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }

                    // Store new file
                    $path = $file->store('settings', 'public');
                    $setting->value = $path;
                } else {
                    $setting->value = $item['value'];
                }

                $setting->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Settings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating settings: ' . $e->getMessage());
        }
    }

    /**
     * Initialize default settings if not exists.
     *
     * @return void
     */
    public function initialize()
    {
        $defaultSettings = [
            // About Us Page Images
            [
                'key' => 'about_us_image',
                'value' => 'settings/about-us.jpg',
                'group' => 'about_page',
                'type' => 'image',
                'label' => 'About Us Main Image',
                'description' => 'The main image displayed on the About Us page'
            ],
            [
                'key' => 'team_ceo_image',
                'value' => 'settings/team-ceo.jpg',
                'group' => 'about_page',
                'type' => 'image',
                'label' => 'CEO Image',
                'description' => 'Image of the CEO on the About Us page'
            ],
            [
                'key' => 'team_cto_image',
                'value' => 'settings/team-cto.jpg',
                'group' => 'about_page',
                'type' => 'image',
                'label' => 'CTO Image',
                'description' => 'Image of the CTO on the About Us page'
            ],
            [
                'key' => 'team_marketing_image',
                'value' => 'settings/team-marketing.jpg',
                'group' => 'about_page',
                'type' => 'image',
                'label' => 'Marketing Director Image',
                'description' => 'Image of the Marketing Director on the About Us page'
            ],
            [
                'key' => 'team_support_image',
                'value' => 'settings/team-support.jpg',
                'group' => 'about_page',
                'type' => 'image',
                'label' => 'Support Lead Image',
                'description' => 'Image of the Support Lead on the About Us page'
            ],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
