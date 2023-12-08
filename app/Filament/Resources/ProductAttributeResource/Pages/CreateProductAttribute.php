<?php

namespace App\Filament\Resources\ProductAttributeResource\Pages;

use App\Filament\Resources\ProductAttributeResource;
use CactusGalaxy\FilamentAstrotomic\Resources\Pages\Record\CreateTranslatable;
use Filament\Resources\Pages\CreateRecord;

class CreateProductAttribute extends CreateRecord
{
    use CreateTranslatable;

    protected static string $resource = ProductAttributeResource::class;

    protected function getActions(): array
    {
        return [
        ];
    }
}
