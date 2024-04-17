<?php

namespace App\Filament\Concerns;

use Illuminate\Contracts\Support\Htmlable;

trait ConfigurePageLabel
{
    public static function getNavigationLabel(): string
    {
        $slug = static::getSlug();

        return __("filament/pages/{$slug}.label");
    }

    public function getTitle(): Htmlable|string
    {
        $slug = static::getSlug();

        return __("filament/pages/$slug.title");
    }
}
