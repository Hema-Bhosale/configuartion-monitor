<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConfigurationChanged
{
    use Dispatchable, SerializesModels;

    public $changeEventId;
    public $filePath;
    public $changes;

    public function __construct(
        int $changeEventId,
        string $filePath,
        array $changes
    ) {
        $this->changeEventId = $changeEventId;
        $this->filePath = $filePath;
        $this->changes = $changes;
    }
}