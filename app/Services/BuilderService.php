<?php

namespace App\Services;

use App\Models\Invitation;

class BuilderService
{
    public function buildPayload(Invitation $invitation): array
    {
        return [
            'id' => $invitation->id,
            'title' => $invitation->title,
            'slug' => $invitation->slug,
            'theme_id' => $invitation->theme_id,
            'is_published' => (bool) $invitation->is_published,
        ];
    }
}
