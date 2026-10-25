<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSiteSettingsRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    /**
     * Display the site settings management view.
     */
    public function index(): View
    {
        $settings = SiteSetting::all()->keyBy('key');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update dynamic site settings in storage.
     */
    public function update(UpdateSiteSettingsRequest $request): RedirectResponse
    {
        $inputs = $request->validated('settings');

        if ($request->hasFile('site_logo_file')) {
            $path = $request->file('site_logo_file')->store('logos', 'public');
            $inputs['site_logo_url'] = '/storage/'.$path;
        } elseif ($request->boolean('remove_site_logo')) {
            $inputs['site_logo_url'] = '';
        }

        if ($request->hasFile('hero_image_file')) {
            $path = $request->file('hero_image_file')->store('hero', 'public');
            $inputs['hero_image_url'] = '/storage/'.$path;
        }

        foreach ($inputs as $key => $value) {
            SiteSetting::set($key, $value);
        }

        SiteSetting::clearRuntimeCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan tampilan & konten dinamis berhasil disimpan!');
    }
}
