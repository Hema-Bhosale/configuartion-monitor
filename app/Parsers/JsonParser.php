<?php

namespace App\Parsers;

use App\Contracts\ConfigParserInterface;
use RuntimeException;

class JsonParser implements ConfigParserInterface
{
    public function parse(string $path): array
    {
        if (!is_readable($path)) {
            throw new RuntimeException(
                "JSON file is not readable: {$path}"
            );
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException(
                "Unable to read JSON file: {$path}"
            );
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                'Invalid JSON: ' . json_last_error_msg()
            );
        }

        if (!is_array($data)) {
            throw new RuntimeException(
                'JSON root must be an object or array.'
            );
        }

        return $data;
    }
}