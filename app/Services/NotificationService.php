<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a WhatsApp message via Fonnte.
     *
     * @param  string  $to  Phone number with country code, e.g. 628123456789
     */
    public function sendWhatsApp(string $to, string $message): bool
    {
        $token = config('services.fonnte.token');

        if (empty($token)) {
            Log::warning('Fonnte token is not configured — WhatsApp not sent.', compact('to'));

            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $to,
                'message'     => $message,
                'countryCode' => '62',
            ]);

            if (! $response->successful()) {
                Log::error('Fonnte send failed', [
                    'to'     => $to,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Fonnte HTTP exception', ['to' => $to, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
