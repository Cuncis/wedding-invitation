<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SETTLEMENT = 'settlement';
    public const STATUS_EXPIRE = 'expire';
    public const STATUS_CANCEL = 'cancel';
    public const STATUS_DENY = 'deny';
    public const STATUS_REFUND = 'refund';

    protected $fillable = [
        'order_id',
        'gateway',
        'external_id',
        'payment_method',
        'status',
        'amount',
        'raw_payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'raw_payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
