<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Cms;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.content.settings', ['groups' => config('cms.settings'), 'values' => Cms::settings()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $keys = collect(config('cms.settings'))->flatten(1)->pluck(0);
        $rules = $keys->mapWithKeys(fn ($k) => [$k => ['nullable', 'string', 'max:2000']])->all();
        $data = $request->validate($rules);

        foreach ($keys as $key) {
            $value = $data[$key] ?? null;
            if ($value === null || trim($value) === '') {
                Setting::where('key', $key)->delete();
            } else {
                Setting::updateOrCreate(['key' => $key], ['value' => trim($value)]);
            }
        }
        Cms::forgetSettings();

        return back()->with('status', 'Settings saved.');
    }
}
