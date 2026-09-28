<?php

namespace App\Parsers;

use App\Contracts\ConfigParserInterface;
use RuntimeException;

class PropertiesParser implements ConfigParserInterface
{
    public function parse(string $path): array
    {
        if (!is_readable($path)) {
            throw new RuntimeException(
                "Properties file is not readable: {$path}"
            );
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);

        if ($lines === false) {
            throw new RuntimeException(
                "Unable to read properties file: {$path}"
            );
        }

        $result = [];

        foreach ($lines as $line) {

            $line = trim($line);

            if ($line === '') {
                continue;
            }

            // Comments can start with # or !
            if (
                strpos($line, '#') === 0 ||
                strpos($line, '!') === 0
            ) {
                continue;
            }

            // Support key=value and key:value
            $position = strpos($line, '=');

            if ($position === false) {
                $position = strpos($line, ':');
            }

            if ($position === false) {
                continue;
            }

            $key = trim(substr($line, 0, $position));
            $value = trim(substr($line, $position + 1));

            $result[$key] = $value;
        }

        return $result;
    }
}