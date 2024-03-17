<?php

use App\Data\Localised;
use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->inGroup('site', function (SettingsBlueprint $blueprint) {
            $blueprint->add('logo', \Illuminate\Support\Str::after(fake()->image(storage_path('app/public')), 'public/'));

            $blueprint->add('name', new Localised([
                'uk' => 'Назва сайту',
                'en' => 'Site name',
            ]));

            $blueprint->add('description', new Localised([
                'uk' => 'Опис сайту',
                'en' => 'Site description',
            ]));
        });

        $this->migrator->inGroup('footer', function (SettingsBlueprint $blueprint) {
            $blueprint->add('description', new Localised([
                'en' => 'Footer Site description',
                'uk' => 'Footer Опис сайту',
            ]));
        });
    }
};
