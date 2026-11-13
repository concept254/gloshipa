<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Subscriber;
use App\Support\Cms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * One controller for every content type listed in config/cms.php.
 * Add a field there and the list, form, validation and saving all pick it up.
 */
class ContentController extends Controller
{
    public function hub(): View
    {
        $cards = [];
        foreach (config('cms.resources') as $key => $def) {
            $cards[] = ['key' => $key, 'label' => $def['label'], 'count' => Cms::guard(fn () => $def['model']::count(), null)];
        }

        return view('admin.content.hub', [
            'cards' => $cards,
            'unread' => Cms::guard(fn () => Inquiry::whereNull('read_at')->count(), 0),
            'subscribers' => Cms::guard(fn () => Subscriber::whereNull('unsubscribed_at')->count(), 0),
            'ready' => Cms::guard(fn () => \Illuminate\Support\Facades\Schema::hasTable('pages'), false),
        ]);
    }

    public function index(Request $request, string $resource): View
    {
        $def = $this->def($resource);
        [$col, $dir] = $def['order'];
        $rows = $def['model']::query()->orderBy($col, $dir)->orderBy('id')->paginate(25);

        return view('admin.content.index', compact('resource', 'def', 'rows'));
    }

    public function create(string $resource): View
    {
        $def = $this->def($resource);

        return view('admin.content.form', ['resource' => $resource, 'def' => $def, 'item' => new $def['model'], 'fields' => $this->fields($def)]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $def = $this->def($resource);
        $data = $this->validated($request, $def, null);
        if ($resource === 'pages') {
            $data['kind'] = 'content';
        }
        $item = $def['model']::create($data);

        return redirect()->route('admin.website.index', $resource)->with('status', ucfirst($def['singular']) . ' created.');
    }

    public function edit(string $resource, int $id): View
    {
        $def = $this->def($resource);
        $item = $def['model']::findOrFail($id);

        return view('admin.content.form', ['resource' => $resource, 'def' => $def, 'item' => $item, 'fields' => $this->fields($def)]);
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $def = $this->def($resource);
        $item = $def['model']::findOrFail($id);
        $data = $this->validated($request, $def, $item);
        $item->update($data);
        Cms::forgetSettings();

        return redirect()->route('admin.website.index', $resource)->with('status', ucfirst($def['singular']) . ' saved.');
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        $def = $this->def($resource);
        $item = $def['model']::findOrFail($id);

        if ($resource === 'pages' && $item->isSystem()) {
            return back()->with('warning', 'Built-in pages cannot be deleted. Untick "Published" to hide one.');
        }
        foreach ($this->fields($def) as $f) {
            if ($f['type'] === 'image') {
                Cms::deleteImage($item->{$f['name']});
            }
        }
        $item->delete();

        return redirect()->route('admin.website.index', $resource)->with('status', ucfirst($def['singular']) . ' deleted.');
    }

    // ------------------------------------------------------------------

    private function def(string $resource): array
    {
        return config("cms.resources.{$resource}") ?? abort(404);
    }

    /** Normalise the compact field arrays from config/cms.php. */
    private function fields(array $def): array
    {
        return array_map(function ($f) {
            $options = isset($f['options']) ? config('cms.' . $f['options'], []) : [];

            return ['name' => $f[0], 'label' => $f[1], 'type' => $f[2], 'rules' => $f[3], 'hint' => $f['hint'] ?? null, 'default' => $f['default'] ?? null, 'options' => $options];
        }, $def['fields']);
    }

    private function validated(Request $request, array $def, ?Model $item): array
    {
        $fields = $this->fields($def);
        $rules = [];
        foreach ($fields as $f) {
            $rules[$f['name']] = $f['type'] === 'image'
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096']
                : ($f['type'] === 'checkbox' ? ['nullable'] : $f['rules']);
        }

        $table = (new $def['model'])->getTable();
        $isSystemPage = $item && $def['model'] === \App\Models\Page::class && $item->isSystem();
        if (isset($rules['slug'])) {
            $slugRules = is_array($rules['slug']) ? $rules['slug'] : explode('|', $rules['slug']);
            $slugRules[] = Rule::unique($table, 'slug')->ignore($item?->getKey());
            if ($def['model'] === \App\Models\Page::class) {
                $slugRules[] = Rule::notIn(config('cms.reserved_slugs'));
            }
            $rules['slug'] = $slugRules;
        }

        $validated = $request->validate($rules);
        $data = [];

        foreach ($fields as $f) {
            $name = $f['name'];
            if ($f['type'] === 'checkbox') {
                $data[$name] = $request->boolean($name);
            } elseif ($f['type'] === 'image') {
                $old = $item?->{$name};
                if ($request->hasFile($name)) {
                    Cms::deleteImage($old);
                    $data[$name] = Cms::storeImage($request->file($name), $def['upload'] ?? $table);
                } elseif ($request->boolean('remove_' . $name)) {
                    Cms::deleteImage($old);
                    $data[$name] = null;
                }
            } else {
                $data[$name] = $validated[$name] ?? null;
            }
        }

        if ($isSystemPage) {
            unset($data['slug']); // built-in pages keep their address
        } elseif (isset($def['slug_from'])) {
            $base = $data['slug'] ?: Str::slug((string) ($data[$def['slug_from']] ?? 'item'));
            $data['slug'] = $this->uniqueSlug($def['model'], $base ?: 'item', $item?->getKey());
        }
        if (array_key_exists('published_at', $data) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        foreach (['sort_order'] as $k) {
            if (array_key_exists($k, $data) && $data[$k] === null) {
                $data[$k] = 100;
            }
        }

        return $data;
    }

    private function uniqueSlug(string $model, string $slug, mixed $ignoreId): string
    {
        $candidate = $slug;
        $i = 2;
        $reserved = $model === \App\Models\Page::class ? config('cms.reserved_slugs') : [];
        while (in_array($candidate, $reserved, true) || $model::where('slug', $candidate)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $candidate = $slug . '-' . $i++;
        }

        return $candidate;
    }
}
