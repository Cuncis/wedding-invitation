<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationConfig extends Model
{
    protected $fillable = [
        'invitation_id',
        'music_url',
        'font_family',
        'primary_color',
        'custom_css',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
