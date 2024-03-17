<?php

namespace App\Models\Contracts;

interface Singular
{
    public static function getFirstRecord(): self;

    public static function getDefaultValuesForRecord(): array;
}
