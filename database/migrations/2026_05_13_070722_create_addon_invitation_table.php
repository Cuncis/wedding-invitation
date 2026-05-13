<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot table: many-to-many between invitations and addons.
     *
     * Records which addons a user has selected for an invitation.
     * price_at_purchase is stored so historical orders remain accurate
     * even if the addon price changes later.
     *
     * Usage in Eloquent:
     *   $invitation->addons()->attach($addonId, ['price_at_purchase' => $price]);
     *   $invitation->addons  // returns Collection of Addon models
     */
    public function up(): void
    {
        Schema::create('addon_invitation', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invitation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('addon_id')
                ->constrained()
                ->cascadeOnDelete();

            // Snapshot of price at time of selection — protects against price changes
            $table->unsignedInteger('price_at_purchase')->default(0);

            $table->timestamps();

            // One addon per invitation max
            $table->unique(['invitation_id', 'addon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addon_invitation');
    }
};
