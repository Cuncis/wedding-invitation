<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimationPack extends Model
{
    use HasFactory;
    public const KEY_FREE = 'free';
    public const KEY_STANDARD = 'standard';
    public const KEY_PREMIUM = 'premium';

    protected $fillable = [
        'name',
        'key',
        'description',
        'features',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'price' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
