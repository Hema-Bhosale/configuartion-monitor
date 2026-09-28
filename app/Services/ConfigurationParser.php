<?php

namespace App\Services;

use App\Parsers\EnvParser;
use App\Parsers\JsonParser;
use App\Parsers\PropertiesParser;
use App\Parsers\YamlParser;
use RuntimeException;

class ConfigurationParser
{
    public function parse(string $path): array
    {
         // Handle .env files first
        $filename = basename($path);

        if ($filename === '.env' || strpos($filename, '.env.') === 0) {
            $parser = new EnvParser();

            return $parser->parse($path);
        }
        $extension = strtolower(
            pathinfo($path, PATHINFO_EXTENSION)
        );

        switch ($extension) {

            case 'json':
                $parser = new JsonParser();
                break;

            case 'yaml':
            case 'yml':
                $parser = new YamlParser();
                break;

            case 'env':
                $parser = new EnvParser();
                break;

            case 'properties':
                $parser = new PropertiesParser();
                break;

            default:
                throw new RuntimeException(
                    "Unsupported configuration file type: .{$extension}"
                );
        }

        return $parser->parse($path);
    }
}