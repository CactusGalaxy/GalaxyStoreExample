<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Stringable;

class Localised extends Data implements Stringable
{
    public function __construct(
        public array $translations = [],
    ) {
    }

    public function value(): mixed
    {
        return $this->translations[app()->getLocale()] ?? null;
    }

    public function __toString(): string
    {
        return $this->value() ?: '';
    }
}
