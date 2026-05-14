<?php

namespace Tests\Feature;

use App\Jobs\SendRsvpNotification;
use App\Models\Invitation;
use App\Models\RsvpResponse;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SendRsvpNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rsvp_submission_dispatches_notification_job(): void
    {
        Queue::fake();

        $invitation = Invitation::factory()->published()->create(['slug' => 'notif-test']);

        $this->post('/rsvp/notif-test', [
            'guest_name' => 'Budi Santoso',
            'attendance' => 'hadir',
            'pax'        => 2,
        ])->assertRedirect();

        Queue::assertPushed(SendRsvpNotification::class, function ($job) {
            return $job->rsvp->guest_name === 'Budi Santoso';
        });
    }

    public function test_job_sends_whatsapp_to_couple_with_whatsapp_number(): void
    {
        Config::set('services.fonnte.token', 'fake-token');

        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true], 200),
        ]);

        $user = User::factory()->create(['whatsapp' => '628111222333']);
        $invitation = Invitation::factory()->for($user)->published()->create(['slug' => 'wa-job-test']);
        $rsvp = RsvpResponse::factory()->create([
            'invitation_id' => $invitation->id,
            'guest_name'    => 'Sari Dewi',
            'attendance'    => 'hadir',
            'pax'           => 1,
        ]);

        $job = new SendRsvpNotification($rsvp);
        $job->handle(app(NotificationService::class));

        Http::assertSent(function ($request) {
            return $request['target'] === '628111222333'
                && str_contains($request['message'], 'Sari Dewi');
        });
    }

    public function test_job_skips_when_user_has_no_whatsapp(): void
    {
        Http::fake();

        $user = User::factory()->create(['whatsapp' => null]);
        $invitation = Invitation::factory()->for($user)->published()->create(['slug' => 'no-wa-test']);
        $rsvp = RsvpResponse::factory()->create(['invitation_id' => $invitation->id]);

        $job = new SendRsvpNotification($rsvp);
        $job->handle(app(NotificationService::class));

        Http::assertNothingSent();
    }
}
