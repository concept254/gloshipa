<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** File types accepted from the hero folder. */
    private const HERO_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'svg'];

    public function index(): View
    {
        return view('home', [
            'heroImage' => $this->heroImage(),
        ]);
    }

    private function heroImage(): string
    {
        $folder = public_path('images/hero');

        if (File::isDirectory($folder)) {
            $images = collect(File::files($folder))
                ->filter(fn ($file) => in_array(strtolower($file->getExtension()), self::HERO_EXTENSIONS, true))
                ->values();

            if ($images->isNotEmpty()) {
                return asset('images/hero/' . rawurlencode($images->random()->getFilename()));
            }
        }

        // No local images found, use a random placeholder
        return 'https://picsum.photos/seed/' . random_int(1, 1000) . '/1200/900';
    }
}