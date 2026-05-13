<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stores the full JSON builder config for each invitation.
     *
     * Kept in a separate table (1-to-1 with invitations) so the
     * invitations table stays lean for list queries. The config JSON
     * can grow large as guests fill content.
     *
     * Example config shape:
     * {
     *   "colors": { "primary": "#c0392b", "secondary": "#f5f0e8" },
     *   "fonts":  { "heading": "Fraunces", "body": "DM Mono" },
     *   "sections": [
     *     {
     *       "type": "cover",
     *       "visible": true,
     *       "content": {
     *         "groom_name": "Budi",
     *         "bride_name": "Ani",
     *         "date": "2025-12-25",
     *         "time": "10:00"
     *       }
     *     },
     *     {
     *       "type": "venue",
     *       "visible": true,
     *       "content": {
     *         "name": "Grand Ballroom Hotel XYZ",
     *         "address": "Jl. Sudirman No. 1, Jakarta",
     *         "maps_url": "https://maps.google.com/?q=..."
     *       }
     *     },
     *     { "type": "love_story", "visible": false, "content": {} },
     *     { "type": "gallery",    "visible": false, "content": {} },
     *     { "type": "rsvp",       "visible": true,  "content": {} },
     *     { "type": "gift",       "visible": false, "content": {
     *         "bank_name": "BCA",
     *         "account_number": "1234567890",
     *         "account_name": "Budi Santoso"
     *       }
     *     }
     *   ]
     * }
     */
    public function up(): void
    {
        Schema::create('invitation_configs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invitation_id')
                ->unique()  // strictly 1-to-1
                ->constrained()
                ->cascadeOnDelete();

            // Full builder state as JSON
            $table->json('config')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_configs');
    }
};
