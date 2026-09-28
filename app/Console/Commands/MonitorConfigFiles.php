<?php

namespace App\Console\Commands;

use App\Models\MonitoredFile;
use App\Services\ConfigurationMonitor;
use Illuminate\Console\Command;

class MonitorConfigFiles extends Command
{
    protected $signature = 'config:monitor';

    protected $description = 'Monitor configuration files for changes';

    public function handle(ConfigurationMonitor $monitor)
    {
        $this->info('Configuration watcher started...');

        while (true) {

            $files = MonitoredFile::where('status', 'active')->get();

            foreach ($files as $file) {
                try {
                    $monitor->check($file);
                } catch (\Throwable $e) {

                    \Log::error('Watcher error', [
                        'file' => $file->path,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            sleep(1);
        }
    }
}