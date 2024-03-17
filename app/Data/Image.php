<?php

namespace App\Data;

use DragonCode\Contracts\Support\Stringable;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Data;

class Image extends Data implements Stringable
{
    public function __construct(
        public string|null $path = null,
    ) {
    }

    public function getUri(): ?string
    {
        return $this->path
            ? Storage::disk('public')->url($this->path)
            : null;
    }

    public function __toString(): string
    {
        return $this->getUri() ?: '';
    }
}
