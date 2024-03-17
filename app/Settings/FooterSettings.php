<?php

namespace App\Settings;

use App\Data\Localised;
use Spatie\LaravelSettings\Settings;

class FooterSettings extends Settings
{
    public Localised $description;

    public static function group(): string
    {
        return 'footer';
    }
}
