<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $invitations = Invitation::where('user_id', $request->user()?->id)
            ->latest()
            ->paginate(10);

        return response()->json($invitations);
    }

    public function store(StoreInvitationRequest $request): JsonResponse
    {
        $invitation = Invitation::create([
            ...$request->validated(),
            'user_id' => $request->user()?->id,
        ]);

        return response()->json($invitation, 201);
    }

    public function show(Invitation $invitation): JsonResponse
    {
        $this->authorize('view', $invitation);

        return response()->json($invitation);
    }

    public function update(StoreInvitationRequest $request, Invitation $invitation): JsonResponse
    {
        $this->authorize('update', $invitation);

        $invitation->update($request->validated());

        return response()->json($invitation->refresh());
    }

    public function destroy(Invitation $invitation): JsonResponse
    {
        $this->authorize('delete', $invitation);

        $invitation->delete();

        return response()->json(status: 204);
    }
}
