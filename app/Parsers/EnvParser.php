<?php

namespace App\Parsers;

use App\Contracts\ConfigParserInterface;
use RuntimeException;

class EnvParser implements ConfigParserInterface
{
    public function parse(string $path): array
    {
        if (!is_readable($path)) {
            throw new RuntimeException(
                "ENV file is not readable: {$path}"
            );
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);

        if ($lines === false) {
            throw new RuntimeException(
                "Unable to read ENV file: {$path}"
            );
        }

        $result = [];

        foreach ($lines as $line) {

            $line = trim($line);

            // Empty line
            if ($line === '') {
                continue;
            }

            // Comment
            if (strpos($line, '#') === 0) {
                continue;
            }

            // Find first =
            $position = strpos($line, '=');

            if ($position === false) {
                continue;
            }

            $key = trim(substr($line, 0, $position));
            $value = trim(substr($line, $position + 1));

            // Remove surrounding quotes
            if (
                strlen($value) >= 2 &&
                (
                    ($value[0] === '"' && $value[strlen($value) - 1] === '"') ||
                    ($value[0] === "'" && $value[strlen($value) - 1] === "'")
                )
            ) {
                $value = substr($value, 1, -1);
            }

            $result[$key] = $value;
        }

        return $result;
    }
}