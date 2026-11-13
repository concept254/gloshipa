<?php

namespace App\Support;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Small helper layer for the public site. Available in Blade as `Cms::`.
 * Everything here is defensive: if the new tables do not exist yet (code deployed
 * before `php artisan migrate`), the site falls back to defaults instead of erroring.
 */
class Cms
{
    private const CACHE_KEY = 'cms.settings';

    /** Run a database-backed lookup; return $default if it fails. */
    public static function guard(callable $fn, mixed $default = null): mixed
    {
        return rescue($fn, $default, false);
    }

    /** @return array<string,string|null> */
    public static function settings(): array
    {
        return self::guard(fn () => Cache::rememberForever(self::CACHE_KEY, fn () => Setting::query()->pluck('value', 'key')->all()), []);
    }

    public static function setting(string $key, mixed $default = null): mixed
    {
        $value = self::settings()[$key] ?? null;

        return ($value === null || trim((string) $value) === '') ? $default : $value;
    }

    public static function forgetSettings(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function phone(): string
    {
        return (string) self::setting('company_phone', config('gloshipa.contact.phone'));
    }

    public static function email(): string
    {
        return (string) self::setting('company_email', config('gloshipa.contact.email'));
    }

    public static function address(): string
    {
        return (string) self::setting('company_address', config('gloshipa.contact.address'));
    }

    public static function inquiryEmail(): string
    {
        return (string) self::setting('inquiry_email', config('gloshipa.admin_email'));
    }

    /** wa.me link, or null when no number is set. */
    public static function whatsappUrl(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) self::setting('whatsapp_number', ''));
        if ($digits === '' || strlen($digits) < 7) {
            return null;
        }
        $text = (string) self::setting('whatsapp_message', 'Hello Glo Shipa, I would like some help with a shipment.');

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode($text);
    }

    /** Only http(s) map embeds are allowed. */
    public static function mapUrl(): ?string
    {
        $url = trim((string) self::setting('map_embed_url', ''));

        return preg_match('#^https://#i', $url) ? $url : null;
    }

    public static function socials(): array
    {
        $out = [];
        foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'x' => 'X', 'youtube' => 'YouTube'] as $key => $label) {
            $url = trim((string) self::setting('social_' . $key, ''));
            if (preg_match('#^https?://#i', $url)) {
                $out[$label] = $url;
            }
        }

        return $out;
    }

    /** Top menu: published pages flagged for the menu. Falls back to the built-in set. */
    public static function menu(): array
    {
        return self::guard(function () {
            $pages = Page::published()->where('show_in_menu', true)->orderBy('sort_order')->orderBy('id')->get();

            return $pages->isEmpty() ? self::fallbackMenu() : $pages->map(fn (Page $p) => ['label' => $p->title, 'url' => $p->url(), 'slug' => $p->slug])->all();
        }, self::fallbackMenu());
    }

    public static function footerPages(): array
    {
        return self::guard(fn () => Page::published()->where('show_in_footer', true)->orderBy('sort_order')->orderBy('id')
            ->get()->map(fn (Page $p) => ['label' => $p->title, 'url' => $p->url()])->all(), []);
    }

    private static function fallbackMenu(): array
    {
        return array_map(fn ($row) => ['label' => $row[0], 'url' => route($row[1]), 'slug' => $row[2]], [
            ['About', 'site.about', 'about'], ['Services', 'site.services', 'services'], ['News', 'site.news', 'news'], ['Contact', 'site.contact', 'contact'],
        ]);
    }

    /** Page record for a built-in page (title, intro and header image are editable). */
    public static function page(string $slug): ?Page
    {
        return self::guard(fn () => Page::where('slug', $slug)->first());
    }

    // ----- images -----

    public static function uploadPath(): string
    {
        return rtrim(config('cms.upload_path') ?: public_path('uploads'), '/\\');
    }

    public static function uploadUrl(): string
    {
        return rtrim(config('cms.upload_url') ?: asset('uploads'), '/');
    }

    public static function image(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        if (preg_match('#^https?://#i', $value)) {
            return $value;
        }

        return self::uploadUrl() . '/' . ltrim($value, '/');
    }

    /** Header image for a page: its own, else a random bundled one, else a random online photo. */
    public static function headerImage(?string $own, string $seed = 'glo-shipa'): string
    {
        if ($own) {
            return self::image($own);
        }
        $files = array_values(array_filter(config('cms.header_images', []), fn ($f) => is_file(public_path('images/headers/' . $f))));
        if ($files) {
            return asset('images/headers/' . $files[array_rand($files)]);
        }

        return 'https://picsum.photos/seed/' . rawurlencode($seed) . '/1600/600';
    }

    public static function markdown(?string $text): string
    {
        return Str::markdown((string) $text, ['html_input' => 'strip', 'allow_unsafe_links' => false]);
    }

    /** Stores an uploaded image and returns its relative path. SVG is refused on purpose (script risk). */
    public static function storeImage(\Illuminate\Http\UploadedFile $file, string $folder): string
    {
        $dir = self::uploadPath() . '/' . $folder;
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $name = Str::random(24) . '.' . $ext;
        $file->move($dir, $name);

        return $folder . '/' . $name;
    }

    public static function deleteImage(?string $value): void
    {
        if ($value && ! preg_match('#^https?://#i', $value) && ! str_contains($value, '..')) {
            @unlink(self::uploadPath() . '/' . ltrim($value, '/'));
        }
    }

    public static function icon(string $name): string
    {
        $paths = [
            'ship' => 'M3 17l2 3h14l2-3M5 17V11l7-3 7 3v6M12 8V3M8 11h8',
            'plane' => 'M10.5 13.5L3 11l1-2 8 .5 5-5a2 2 0 013 3l-5 5 .5 8-2 1-2.5-7.5-4 4-1-1 4-4z',
            'truck' => 'M3 6h11v10H3zM14 9h4l3 3v4h-7M7 19a2 2 0 100-4 2 2 0 000 4zM17 19a2 2 0 100-4 2 2 0 000 4z',
            'box' => 'M21 8l-9-5-9 5v8l9 5 9-5V8zM3 8l9 5 9-5M12 13v8',
            'warehouse' => 'M3 10l9-6 9 6v10H3zM8 20v-7h8v7M8 16h8',
            'doc' => 'M7 3h7l5 5v13H7zM14 3v5h5M10 13h6M10 17h6',
            'shield' => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6zM9 12l2 2 4-4',
            'globe' => 'M12 21a9 9 0 100-18 9 9 0 000 18zM3 12h18M12 3c2.5 2.5 3.5 5.5 3.5 9s-1 6.5-3.5 9c-2.5-2.5-3.5-5.5-3.5-9S9.5 5.5 12 3z',
            'clock' => 'M12 21a9 9 0 100-18 9 9 0 000 18zM12 7v5l3 2',
            'anchor' => 'M12 6a2 2 0 100-4 2 2 0 000 4zM12 6v15M5 13a7 7 0 0014 0M8 11H5v2M19 13v-2h-3',
        ];

        return $paths[$name] ?? $paths['box'];
    }
}
