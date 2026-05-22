<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationConfig extends Model
{
    protected $fillable = [
        'invitation_id',
        'theme_id',
        'animation_pack_id',
        'sections',
        'colors',
        'typography',
        'content',
        'addon_ids',
        'music',
        'maps',
    ];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'colors' => 'array',
            'typography' => 'array',
            'content' => 'array',
            'addon_ids' => 'array',
            'music' => 'array',
            'maps' => 'array',
            'countdown' => 'array',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function animationPack(): BelongsTo
    {
        return $this->belongsTo(AnimationPack::class);
    }
}
