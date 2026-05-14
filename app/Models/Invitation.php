<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invitation extends Model
{
    use HasFactory;
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'slug',
        'groom_name',
        'bride_name',
        'event_date',
        'event_venue',
        'status',
        'published_at',
        'expires_at',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'view_count' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
            ->where('status', self::STATUS_ACTIVE)
            ->where('expires_at', '>', now());
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null
            && $this->status === self::STATUS_ACTIVE
            && $this->expires_at !== null
            && $this->expires_at->isFuture();
    }

    public function coupleNames(): string
    {
        return trim($this->groom_name . ' & ' . $this->bride_name);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function config(): HasOne
    {
        return $this->hasOne(InvitationConfig::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function rsvpResponses(): HasMany
    {
        return $this->hasMany(RsvpResponse::class);
    }
}
