<?php

namespace App\Settings;

use App\Data\Localised;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{
    public ?string $logo;

    public Localised $name;

    public Localised $description;

    public static function group(): string
    {
        return 'site';
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? Storage::disk('public')->url($this->logo) : null;
    }
}
