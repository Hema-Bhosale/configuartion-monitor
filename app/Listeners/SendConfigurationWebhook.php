<?php

namespace App\Listeners;

use App\Events\ConfigurationChanged;
use App\Jobs\SendConfigurationWebhookJob;
use Illuminate\Support\Facades\Log;

class SendConfigurationWebhook
{
    public function handle(ConfigurationChanged $event)
    {
        Log::info('ConfigurationChanged event received', [
            'change_event_id' => $event->changeEventId,
        ]);

        SendConfigurationWebhookJob::dispatch(
            $event->changeEventId
        );

        Log::info('Webhook job dispatched', [
            'change_event_id' => $event->changeEventId,
        ]);
    }
}