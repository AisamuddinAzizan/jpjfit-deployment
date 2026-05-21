<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LandingPageImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'image_path',
        'sort_order',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function getImageUrlAttribute(): string
    {
        if (Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        return asset('storage/'.ltrim($this->image_path, '/'));
    }
}
