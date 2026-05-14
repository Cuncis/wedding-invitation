<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    use HasFactory;
    public const CATEGORY_MEDIA = 'media';
    public const CATEGORY_INTERACTIVE = 'interactive';
    public const CATEGORY_SOCIAL = 'social';
    public const CATEGORY_UTILITY = 'utility';

    protected $fillable = [
        'name',
        'key',
        'description',
        'icon',
        'price',
        'category',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
