<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $invitations = $user->invitations()->latest()->get();

        $stats = [
            'total' => $invitations->count(),
            'active' => $invitations->where('status', Invitation::STATUS_ACTIVE)->count(),
            'draft' => $invitations->where('status', Invitation::STATUS_DRAFT)->count(),
        ];

        return view('dashboard.index', compact('invitations', 'stats'));
    }

    public function create(Request $request): RedirectResponse
    {
        $invitation = $request->user()->invitations()->create([
            'groom_name' => '',
            'bride_name' => '',
            'status' => Invitation::STATUS_DRAFT,
        ]);

        // Create empty config row (1-1)
        $invitation->config()->create([]);

        return redirect()->route('builder.edit', $invitation)
            ->with('success', 'Undangan draft berhasil dibuat. Silakan lengkapi datanya.');
    }

    public function destroy(Invitation $invitation): RedirectResponse
    {
        $this->authorize('delete', $invitation);

        if ($invitation->status === Invitation::STATUS_ACTIVE) {
            return redirect()->route('dashboard')
                ->with('error', 'Undangan yang sudah aktif tidak dapat dihapus.');
        }

        $invitation->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Undangan berhasil dihapus.');
    }
}
