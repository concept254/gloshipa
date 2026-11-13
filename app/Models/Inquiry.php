<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $guarded = ['id', 'read_at'];

    protected function casts(): array
    {
        return ['meta' => 'array', 'read_at' => 'datetime'];
    }
}
