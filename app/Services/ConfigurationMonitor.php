<?php

namespace App\Services;

use App\Events\ConfigurationChanged;
use App\Models\ChangeEvent;
use App\Models\MonitoredFile;
use Illuminate\Support\Facades\Log;

class ConfigurationMonitor
{
    private $parser;

    public function __construct(ConfigurationParser $parser)
    {
        $this->parser = $parser;
    }

    public function check(MonitoredFile $monitoredFile)
    {
        $path = $monitoredFile->path;

        /*
         * Check whether the monitored file still exists
         */
        if (!file_exists($path)) {
            Log::warning('Configuration file deleted', [
                'file' => $path,
            ]);

            return;
        }

        /*
         * Generate current file hash
         */
        $currentHash = hash_file('sha256', $path);

        /*
         * Update last checked time
         */
        $monitoredFile->update([
            'last_checked_at' => now(),
        ]);

        /*
         * No change detected
         */
        if ($currentHash === $monitoredFile->last_hash) {
            return;
        }

        Log::info('Configuration file changed', [
            'file' => $path,
        ]);

        /*
         * Parse the changed configuration
         */
        $snapshot = $this->parser->parse($path);

        /*
         * Detect individual configuration changes
         */
        $changes = $this->detectChanges(
            $monitoredFile->last_snapshot ?? [],
            $snapshot
        );

        Log::info('Configuration parsed successfully', [
            'file' => $path,
            'snapshot' => $snapshot,
        ]);

        /*
         * Create persistent change event
         */
        Log::info('About to create ChangeEvent', [
            'monitored_file_id' => $monitoredFile->id,
            'file_path' => $path,
            'changes' => $changes,
        ]);

        $changeEvent = ChangeEvent::create([
            'monitored_file_id' => $monitoredFile->id,
            'file_path' => $path,
            'event_type' => 'configuration.changed',
            'changes' => $changes,
            'detected_at' => now(),
            'webhook_status' => 'pending',
        ]);

        Log::info('ChangeEvent created successfully', [
            'file' => $path,
            'monitored_file_id' => $monitoredFile->id,
            'change_event_id' => $changeEvent->id,
        ]);

        /*
         * Dispatch configuration changed event
         */
        ConfigurationChanged::dispatch(
            $changeEvent->id,
            $path,
            $changes
        );

        /*
         * Save the latest file state
         */
        $monitoredFile->update([
            'last_hash' => $currentHash,
            'last_snapshot' => $snapshot,
            'last_changed_at' => now(),
        ]);
    }

    private function detectChanges(
        array $old,
        array $new,
        string $prefix = ''
    ): array {
        $changes = [];

        foreach ($new as $key => $newValue) {

            $fullKey = $prefix
                ? $prefix . '.' . $key
                : $key;

            $oldValue = $old[$key] ?? null;

            /*
             * Recursively compare nested configuration
             */
            if (is_array($newValue) && is_array($oldValue)) {

                $changes = array_merge(
                    $changes,
                    $this->detectChanges(
                        $oldValue,
                        $newValue,
                        $fullKey
                    )
                );

            } elseif ($oldValue !== $newValue) {

                $changes[$fullKey] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }
}