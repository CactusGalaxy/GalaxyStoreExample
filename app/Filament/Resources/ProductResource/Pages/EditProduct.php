<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use CactusGalaxy\FilamentAstrotomic\Resources\Pages\Record\EditTranslatable;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    use EditTranslatable;

    protected static string $resource = ProductResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Товар';
    }

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
