<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RsvpResponse extends Model
{
    public const ATTENDANCE_HADIR = 'hadir';
    public const ATTENDANCE_TIDAK_HADIR = 'tidak_hadir';
    public const ATTENDANCE_MUNGKIN = 'mungkin';

    protected $fillable = [
        'invitation_id',
        'guest_name',
        'guest_phone',
        'attendance',
        'pax',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'pax' => 'integer',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
