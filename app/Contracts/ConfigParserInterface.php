<?php

namespace App\Contracts;

interface ConfigParserInterface
{
    public function parse(string $path): array;
}