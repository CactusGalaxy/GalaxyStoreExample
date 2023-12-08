<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Model
 */
trait HasMediaAttributes
{
    public function getImageUri(string $field = 'image'): ?string
    {
        return $this->getUriForMedia($field);
    }

    public function getUriForMedia(string $field): ?string
    {
        $attr = $this->getAttribute($field);
        // empty or valid url
        if (empty($attr) || filter_var($attr, FILTER_VALIDATE_URL)) {
            return $attr;
        }

        return Storage::disk('public')->url($attr);
    }
}
