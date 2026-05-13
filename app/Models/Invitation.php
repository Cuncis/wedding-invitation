<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invitation extends Model
{
    protected $fillable = [
        'user_id',
        'theme_id',
        'title',
        'slug',
        'groom_name',
        'bride_name',
        'event_date',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function config(): HasOne
    {
        return $this->hasOne(InvitationConfig::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
