<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\JsonResponse;

class PublicInvitationController extends Controller
{
    public function __invoke(string $slug): JsonResponse
    {
        $invitation = Invitation::where('slug', $slug)->where('is_published', true)->firstOrFail();

        return response()->json($invitation);
    }
}
