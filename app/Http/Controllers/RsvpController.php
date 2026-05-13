<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRsvpRequest;
use App\Models\Invitation;
use App\Models\RsvpResponse;
use Illuminate\Http\JsonResponse;

class RsvpController extends Controller
{
    public function store(StoreRsvpRequest $request, Invitation $invitation): JsonResponse
    {
        $rsvp = RsvpResponse::create([
            ...$request->validated(),
            'invitation_id' => $invitation->id,
        ]);

        return response()->json($rsvp, 201);
    }
}
