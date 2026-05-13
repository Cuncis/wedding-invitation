<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stores RSVP responses submitted by wedding guests.
     *
     * No authentication required for guests — they submit via the public
     * invitation URL. The couple views responses in their dashboard.
     *
     * Submitted via: POST /rsvp/{invitation_slug}
     * Viewed at: dashboard → invitation → RSVP tab
     * Exportable: CSV download per invitation
     */
    public function up(): void
    {
        Schema::create('rsvp_responses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invitation_id')
                ->constrained()
                ->cascadeOnDelete();

            // Guest details — no account needed
            $table->string('guest_name');

            // true = attending, false = not attending
            $table->boolean('attending')->default(true);

            // Number of people coming with this guest (including themselves)
            $table->tinyInteger('pax')->unsigned()->default(1);

            // Optional message / ucapan from the guest
            $table->text('message')->nullable();

            // Optional — shown in dashboard so couple can contact guest
            $table->string('phone', 20)->nullable();

            // Guest's IP address — for basic spam prevention
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index('invitation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvp_responses');
    }
};
