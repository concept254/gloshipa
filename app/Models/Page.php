<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'show_in_menu' => 'boolean', 'show_in_footer' => 'boolean'];
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function isSystem(): bool
    {
        return $this->kind === 'system';
    }

    public function url(): string
    {
        $route = config('cms.system_routes.' . $this->slug);

        return $route && \Illuminate\Support\Facades\Route::has($route) ? route($route) : url('/' . $this->slug);
    }
}
