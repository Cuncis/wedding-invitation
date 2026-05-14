<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\AnimationPack;
use App\Models\Invitation;
use App\Models\Theme;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuilderController extends Controller
{
    /**
     * GET /builder/{invitation}/edit — show the Vue builder SPA.
     */
    public function edit(Invitation $invitation): View
    {
        $this->authorize('update', $invitation);

        $invitation->loadMissing('config');

        $themes         = Theme::active()->orderBy('sort_order')->get();
        $addons         = Addon::active()->orderBy('sort_order')->get();
        $animationPacks = AnimationPack::active()->get();

        return view('builder.edit', compact(
            'invitation',
            'themes',
            'addons',
            'animationPacks',
        ));
    }

    /**
     * PUT /builder/{invitation}/config — auto-save from Vue (debounced).
     */
    public function updateConfig(Request $request, Invitation $invitation): JsonResponse
    {
        $this->authorize('update', $invitation);

        $allowed = [
            'theme_id', 'animation_pack_id', 'addon_ids',
            'sections', 'colors', 'typography', 'content', 'music', 'maps',
        ];

        $data = $request->only($allowed);

        $invitation->config()->updateOrCreate(
            ['invitation_id' => $invitation->id],
            $data,
        );

        return response()->json(['status' => 'saved']);
    }

    /**
     * GET /builder/{invitation}/preview — iframe preview (no auth check for UX).
     */
    public function preview(Invitation $invitation): View
    {
        $this->authorize('update', $invitation);

        $invitation->loadMissing('config.theme', 'config.animationPack');

        return view('builder.preview', compact('invitation'));
    }
}
