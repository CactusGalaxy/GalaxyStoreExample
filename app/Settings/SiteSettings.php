<?php

namespace App\Settings;

use App\Data\Localized;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{
    public ?string $logo;

    public Localized $name;

    public Localized $description;

    public static function group(): string
    {
        return 'site';
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? Storage::disk('public')->url($this->logo) : null;
    }
}
