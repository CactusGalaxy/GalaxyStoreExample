<?php

declare(strict_types=1);

namespace App\Enums\ProductAttribute;

use Illuminate\Support\Collection;

enum DisplayType: string
{
    case TEXT = 'text';
    case COLOR = 'color';
    case SIZE = 'size';

    public function isColor(): bool
    {
        return $this == self::COLOR;
    }

    public static function getOptions(): Collection
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $option) => [
                $option->value => __("admin_labels.product_attributes.display_types.{$option->value}"),
            ]);
    }
}
