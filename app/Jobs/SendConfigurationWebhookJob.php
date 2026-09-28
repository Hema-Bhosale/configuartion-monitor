<?php

namespace App\Jobs;

use App\Models\ChangeEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendConfigurationWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [10, 30, 60];

    private $changeEventId;

    public function __construct(int $changeEventId)
    {
        $this->changeEventId = $changeEventId;
    }

    public function handle()
    {
        $changeEvent = ChangeEvent::find($this->changeEventId);

        if (!$changeEvent) {
            Log::warning('ChangeEvent not found', [
                'change_event_id' => $this->changeEventId,
            ]);

            return;
        }

        $webhookUrl = config('services.syncworks.webhook_url');

        $payload = [
            'event' => $changeEvent->event_type,
            'file' => $changeEvent->file_path,
            'changes' => $changeEvent->changes,
            'timestamp' => now()->toIso8601String(),
        ];

        Log::info('Sending configuration webhook', [
            'change_event_id' => $changeEvent->id,
            'attempt' => $this->attempts(),
        ]);

        // Temporary local testing only.
        // Remove withoutVerifying() for production.
        $response = Http::withoutVerifying()
            ->post($webhookUrl, $payload);

        Log::info('Webhook response', [
            'change_event_id' => $changeEvent->id,
            'status' => $response->status(),
            'successful' => $response->successful(),
            'attempt' => $this->attempts(),
        ]);

        if (!$response->successful()) {
            throw new \Exception(
                'Webhook request failed with status '
                . $response->status()
            );
        }

        $changeEvent->update([
            'webhook_status' => 'sent',
            'webhook_sent_at' => now(),
        ]);

        Log::info('ChangeEvent webhook status updated', [
            'change_event_id' => $changeEvent->id,
            'webhook_status' => 'sent',
        ]);
    }
}