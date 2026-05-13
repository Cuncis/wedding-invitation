<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThemeSwitcherController extends Controller
{
    public function __invoke(Request $request, Invitation $invitation): JsonResponse
    {
        $this->authorize('update', $invitation);

        $validated = $request->validate([
            'theme_id' => ['required', 'integer', 'exists:themes,id'],
        ]);

        $invitation->update($validated);

        return response()->json([
            'message' => 'Theme switched successfully.',
            'data' => $invitation->refresh(),
        ]);
    }
}
