<?php

namespace App\Models\Traits;

trait SingularModel
{
    public static function getFirstRecord(): self
    {
        return static::firstOrCreate(static::getDefaultValuesForRecord());
    }

    public static function getDefaultValuesForRecord(): array
    {
        return [];
    }
}
