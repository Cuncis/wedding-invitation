<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateInvitationConfigRequest;
use App\Models\Invitation;
use App\Models\InvitationConfig;
use Illuminate\Http\JsonResponse;

class InvitationConfigController extends Controller
{
    public function __invoke(UpdateInvitationConfigRequest $request, Invitation $invitation): JsonResponse
    {
        $this->authorize('update', $invitation);

        $config = InvitationConfig::query()->updateOrCreate(
            ['invitation_id' => $invitation->id],
            $request->validated()
        );

        return response()->json($config);
    }
}
