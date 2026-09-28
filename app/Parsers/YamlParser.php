<?php

namespace App\Parsers;

use App\Contracts\ConfigParserInterface;
use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlParser implements ConfigParserInterface
{
    public function parse(string $path): array
    {
        if (!is_readable($path)) {
            throw new RuntimeException(
                "YAML file is not readable: {$path}"
            );
        }

        try {
            $data = Yaml::parseFile($path);
        } catch (ParseException $e) {
            throw new RuntimeException(
                'Invalid YAML: ' . $e->getMessage()
            );
        }

        if ($data === null) {
            return [];
        }

        if (!is_array($data)) {
            throw new RuntimeException(
                'YAML root must be an object/map.'
            );
        }

        return $data;
    }
}