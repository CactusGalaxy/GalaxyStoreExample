<?php

namespace App\Filament\Resources\ProductAttributeResource\Pages;

use App\Filament\Resources\ProductAttributeResource;
use CactusGalaxy\FilamentAstrotomic\Resources\Pages\Record\ListTranslatable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductAttributes extends ListRecords
{
    use ListTranslatable;

    protected static string $resource = ProductAttributeResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver(),
        ];
    }
}
