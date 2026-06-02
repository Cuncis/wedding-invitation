<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

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
        return 'UND-'.date('Ymd').'-'.strtoupper(substr(uniqid('', true), -6));
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
