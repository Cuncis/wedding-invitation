<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_number',
        'user_id',
        'invitation_id',
        'theme_price',
        'addon_price',
        'animation_price',
        'total_amount',
        'status',
        'snapshot',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'theme_price' => 'integer',
            'addon_price' => 'integer',
            'animation_price' => 'integer',
            'total_amount' => 'integer',
            'snapshot' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public static function generateNumber(): string
    {
        $count = static::count() + 1;

        return 'UND-' . date('Y') . '-' . str_pad((string) $count, 5, '0', STR_PAD_LEFT);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
