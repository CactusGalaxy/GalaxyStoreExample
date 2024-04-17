<?php

namespace App\Settings;

use App\Data\Localized;
use Spatie\LaravelSettings\Settings;

class FooterSettings extends Settings
{
    public Localized $description;

    public static function group(): string
    {
        return 'footer';
    }
}
