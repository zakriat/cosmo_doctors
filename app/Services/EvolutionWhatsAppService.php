<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionWhatsAppService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $instance;
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = (bool) config('evolution.enabled');
        $this->baseUrl = rtrim((string) config('evolution.url'), '/');
        $this->apiKey = (string) config('evolution.api_key');
        $this->instance = (string) config('evolution.instance');
    }

    public function sendText(?string $phone, string $message): bool
    {
        if (!$this->enabled) {
            Log::info('Evolution WhatsApp skipped: disabled');
            return false;
        }

        $number = $this->normalizePhone($phone);

        if (!$number || !$message || !$this->baseUrl || !$this->apiKey || !$this->instance) {
            Log::warning('Evolution WhatsApp skipped: missing config or phone', [
                'phone' => $phone,
                'base_url_set' => !empty($this->baseUrl),
                'api_key_set' => !empty($this->apiKey),
                'instance' => $this->instance,
            ]);

            return false;
        }

        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
                ->timeout(20)
                ->post("{$this->baseUrl}/message/sendText/{$this->instance}", [
                    'number' => $number,
                    'text' => $message,
                ]);

            if (!$response->successful()) {
                Log::error('Evolution WhatsApp failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'number' => $number,
                ]);

                return false;
            }

            Log::info('Evolution WhatsApp sent', [
                'number' => $number,
                'response' => $response->json(),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Evolution WhatsApp exception', [
                'error' => $e->getMessage(),
                'number' => $number,
            ]);

            return false;
        }
    }

    private function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (!$phone) {
            return null;
        }

        if (str_starts_with($phone, '+')) {
            return substr($phone, 1);
        }

        if (str_starts_with($phone, '00')) {
            return substr($phone, 2);
        }

        return $phone;
    }
}
