<?php

declare(strict_types=1);

namespace App\Filament\Resources\BannerResource\Pages;

use App\Filament\Resources\BannerResource;
use CactusGalaxy\FilamentAstrotomic\Resources\Pages\Record\CreateTranslatable;
use Filament\Resources\Pages\CreateRecord;

class CreateBanner extends CreateRecord
{
    use CreateTranslatable;

    protected static string $resource = BannerResource::class;

    protected function getActions(): array
    {
        return [
        ];
    }
}
