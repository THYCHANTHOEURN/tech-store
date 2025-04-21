<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
        // Make sure basic settings exist
        $this->ensureBasicSettings();

        $group = $request->query('group', 'general');

        $settings = Setting::where('group', $group)->get()->map(function ($setting) {
            $data = $setting->toArray();
            if ($setting->type === 'image') {
                $data['image_url'] = $setting->image_url;
            }
            return $data;
        });

        $groups = Setting::select(columns: 'group')->distinct()->pluck('group');

        return Inertia::render('Dashboard/Settings/Index', [
            'settings'      => $settings,
            'groups'        => $groups,
            'currentGroup'  => $group
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
        // For debugging
        Log::info('Settings update request received', [
            'has_files' => $request->hasFile('files')
        ]);

        $request->validate([
            'settings' => 'required'
        ]);

        // Decode JSON settings data
        $settingsData = json_decode($request->settings, true);

        DB::beginTransaction();

        try {
            foreach ($settingsData as $item) {
                $setting = Setting::find($item['id']);

                if (!$setting) {
                    continue;
                }

                // Handle image uploads with special care for site_logo
                if ($setting->type === 'image' && $request->hasFile("files.{$setting->key}")) {
                    $file = $request->file("files.{$setting->key}");

                    Log::info('Processing image upload', [
                        'key'   => $setting->key,
                        'file'  => $file->getClientOriginalName()
                    ]);

                    // Delete old file if exists, with more robust path checking
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }

                    // Store new file with setting-specific path
                    $path = $file->store('settings', 'public');
                    
                    // For site logo, ensure it's the right format and size
                    if ($setting->key === 'site_logo') {
                        // You could add image manipulation here if needed with Intervention Image
                    }

                    // Update the setting directly
                    $setting->value = $path;
                    $setting->save();

                    // Clear cache for this setting
                    Cache::forget('setting.' . $setting->key);

                    Log::info('Image updated', [
                        'key'   => $setting->key,
                        'path'  => $path
                    ]);
                } else {
                    // For non-image settings or when no new file is uploaded
                    $setting->value = $item['value'];
                    $setting->save();

                    // Clear cache for this setting
                    Cache::forget('setting.' . $setting->key);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Settings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating settings', ['error' => $e->getMessage()]);
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
                'key'           => 'about_us_image',
                'value'         => 'settings/about-us.jpg',
                'group'         => 'about_page',
                'type'          => 'image',
                'label'         => 'About Us Main Image',
                'description'   => 'The main image displayed on the About Us page'
            ],
            [
                'key'           => 'team_ceo_image',
                'value'         => 'settings/team-ceo.jpg',
                'group'         => 'about_page',
                'type'          => 'image',
                'label'         => 'CEO Image',
                'description'   => 'Image of the CEO on the About Us page'
            ],
            // Add name setting for CEO
            [
                'key'           => 'team_ceo_name',
                'value'         => 'John Doe',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'CEO Name',
                'description'   => 'Name of the CEO displayed on the About Us page'
            ],
            [
                'key'           => 'team_cto_image',
                'value'         => 'settings/team-cto.jpg',
                'group'         => 'about_page',
                'type'          => 'image',
                'label'         => 'CTO Image',
                'description'   => 'Image of the CTO on the About Us page'
            ],
            // Add name setting for CTO
            [
                'key'           => 'team_cto_name',
                'value'         => 'Jane Smith',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'CTO Name',
                'description'   => 'Name of the CTO displayed on the About Us page'
            ],
            [
                'key'           => 'team_marketing_image',
                'value'         => 'settings/team-marketing.jpg',
                'group'         => 'about_page',
                'type'          => 'image',
                'label'         => 'Marketing Director Image',
                'description'   => 'Image of the Marketing Director on the About Us page'
            ],
            // Add name setting for Marketing Director
            [
                'key'           => 'team_marketing_name',
                'value'         => 'Michael Johnson',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'Marketing Director Name',
                'description'   => 'Name of the Marketing Director displayed on the About Us page'
            ],
            [
                'key'           => 'team_support_image',
                'value'         => 'settings/team-support.jpg',
                'group'         => 'about_page',
                'type'          => 'image',
                'label'         => 'Support Lead Image',
                'description'   => 'Image of the Support Lead on the About Us page'
            ],
            // Add name setting for Support Lead
            [
                'key'           => 'team_support_name',
                'value'         => 'Sarah Williams',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'Support Lead Name',
                'description'   => 'Name of the Support Lead displayed on the About Us page'
            ],
            // Add positions for each team member
            [
                'key'           => 'team_ceo_position',
                'value'         => 'CEO & Founder',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'CEO Position',
                'description'   => 'Job title of the CEO displayed on the About Us page'
            ],
            [
                'key'           => 'team_cto_position',
                'value'         => 'CTO',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'CTO Position',
                'description'   => 'Job title of the CTO displayed on the About Us page'
            ],
            [
                'key'           => 'team_marketing_position',
                'value'         => 'Marketing Director',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'Marketing Director Position',
                'description'   => 'Job title of the Marketing Director displayed on the About Us page'
            ],
            [
                'key'           => 'team_support_position',
                'value'         => 'Customer Support Lead',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'Support Lead Position',
                'description'   => 'Job title of the Support Lead displayed on the About Us page'
            ],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Ensure basic settings exist.
     *
     * @return void
     */
    public function ensureBasicSettings()
    {
        // Check for site_logo setting
        if (!Setting::where('key', 'site_logo')->exists()) {
            Setting::create([
                'key'           => 'site_logo',
                'value'         => 'settings/site-logo.png',
                'group'         => 'general',
                'type'          => 'image',
                'label'         => 'Site Logo',
                'description'   => 'Your store logo that appears in the header and other places'
            ]);
        }
        
        // Check for site_name setting
        if (!Setting::where('key', 'site_name')->exists()) {
            Setting::create([
                'key'           => 'site_name',
                'value'         => config('app.name', 'Tech Store'),
                'group'         => 'general',
                'type'          => 'text',
                'label'         => 'Site Name',
                'description'   => 'The name of your store that appears in the browser title and throughout the site'
            ]);
        }
    }
}
