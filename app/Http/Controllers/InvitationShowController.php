<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class InvitationShowController extends Controller
{
    public function __invoke(string $slug): View|Response
    {
        $invitation = Invitation::published()
            ->where('slug', $slug)
            ->with(['config.theme', 'config.animationPack'])
            ->firstOrFail();

        $invitation->increment('view_count');

        $config     = $invitation->config;
        $theme      = $config?->theme;
        $themeSlug  = $theme?->slug ?? 'default';

        $rsvpStats = [
            'total'  => $invitation->rsvpResponses()->count(),
            'hadir'  => $invitation->rsvpResponses()->where('attendance', 'hadir')->count(),
            'total_pax' => (int) $invitation->rsvpResponses()->where('attendance', 'hadir')->sum('pax'),
        ];

        $recentRsvp = $invitation->rsvpResponses()->latest()->take(10)->get();

        return view('invitations.show', compact(
            'invitation',
            'config',
            'theme',
            'themeSlug',
            'rsvpStats',
            'recentRsvp',
        ));
    }
}
