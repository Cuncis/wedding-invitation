<?php

namespace App\Http\Controllers;

use App\Jobs\PublishInvitationJob;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;

class InvitationPublishController extends Controller
{
    public function __invoke(Invitation $invitation): JsonResponse
    {
        $this->authorize('update', $invitation);

        PublishInvitationJob::dispatch($invitation->id);

        return response()->json([
            'message' => 'Invitation publish process queued.',
        ]);
    }
}
