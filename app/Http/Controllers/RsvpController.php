<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRsvpRequest;
use App\Jobs\SendRsvpNotification;
use App\Models\Invitation;
use App\Models\RsvpResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RsvpController extends Controller
{
    /**
     * POST /rsvp/{slug} — public, no auth required.
     */
    public function store(StoreRsvpRequest $request, string $slug): RedirectResponse
    {
        $invitation = Invitation::published()->where('slug', $slug)->firstOrFail();

        $rsvp = $invitation->rsvpResponses()->create($request->validated());

        SendRsvpNotification::dispatch($rsvp);

        return back()->with('rsvp_success', 'Terima kasih atas konfirmasi kehadirannya!');
    }

    /**
     * GET /rsvp/{invitation}/responses — owner-only dashboard.
     */
    public function index(Invitation $invitation): View
    {
        $this->authorize('view', $invitation);

        $responses = $invitation->rsvpResponses()->latest()->paginate(25);

        $stats = [
            'total' => $invitation->rsvpResponses()->count(),
            'hadir' => $invitation->rsvpResponses()->where('attendance', 'hadir')->count(),
            'tidak_hadir' => $invitation->rsvpResponses()->where('attendance', 'tidak_hadir')->count(),
            'mungkin' => $invitation->rsvpResponses()->where('attendance', 'mungkin')->count(),
            'total_pax' => (int) $invitation->rsvpResponses()->where('attendance', 'hadir')->sum('pax'),
        ];

        return view('rsvp.dashboard', compact('invitation', 'responses', 'stats'));
    }

    /**
     * GET /rsvp/{invitation}/export — CSV download.
     */
    public function export(Invitation $invitation): StreamedResponse
    {
        $this->authorize('view', $invitation);

        $responses = $invitation->rsvpResponses()->latest()->get();

        $filename = 'rsvp-' . $invitation->slug . '-' . now()->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($responses) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nama Tamu', 'No. HP', 'Kehadiran', 'Jumlah Tamu', 'Pesan', 'Tanggal']);
            foreach ($responses as $r) {
                fputcsv($handle, [
                    $r->guest_name,
                    $r->guest_phone ?? '',
                    $r->attendance,
                    $r->pax,
                    $r->message ?? '',
                    $r->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
