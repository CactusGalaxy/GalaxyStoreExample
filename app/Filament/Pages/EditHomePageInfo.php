<?php

namespace App\Filament\Pages;

use App\Models\HomePageInfo;
use CactusGalaxy\FilamentAstrotomic\Forms\Components\TranslatableTabs;
use CactusGalaxy\FilamentAstrotomic\TranslatableTab;
use Filament\Forms;
use Filament\Forms\Form;

class EditHomePageInfo extends EditPage
{
    public static function getNavigationGroup(): ?string
    {
        return __('filament/navigation.groups.content');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament/pages/edit-home-page-info.sections.main_banner'))->columns()->schema([
                    Forms\Components\FileUpload::make('hero_section.image.path')
                        ->required()
                        ->columnSpan(1)
                        ->hiddenLabel()
                        ->disk('public')
                        ->image()
                        ->imageEditor()
                        ->downloadable()
                        ->openable()
                        ->imagePreviewHeight('500'),

                    TranslatableTabs::make('Heading')
                        ->columnSpan(1)
                        ->localeTabSchema(fn (TranslatableTab $tab) => [
                            Forms\Components\Grid::make()->schema([
                                Forms\Components\TextInput::make("hero_section.textLeft.translations.{$tab->getLocale()}")
                                    ->maxLength(255)
                                    ->hiddenLabel()
                                    ->required(),
                                Forms\Components\TextInput::make("hero_section.textRight.translations.{$tab->getLocale()}")
                                    ->maxLength(255)
                                    ->hiddenLabel()
                                    ->required(),
                            ]),
                            Forms\Components\TextInput::make("hero_section.quote.translations.{$tab->getLocale()}")
                                ->maxLength(255)
                                ->hiddenLabel()
                                ->required(),
                        ]),
                ])->collapsed(),

                Forms\Components\Section::make(__('filament/pages/edit-home-page-info.sections.promo_info'))->schema([
                    Forms\Components\Grid::make(4)->schema([
                        TranslatableTabs::make('Heading')
                            ->localeTabSchema(fn (TranslatableTab $tab) => [
                                Forms\Components\TextInput::make("promo_section.title.translations.{$tab->getLocale()}")
                                    ->maxLength(255)
                                    ->hiddenLabel()
                                    ->required(),
                                Forms\Components\RichEditor::make("promo_section.description.translations.{$tab->getLocale()}")
                                    ->maxLength(255)
                                    ->hiddenLabel()
                                    ->required(),
                            ])
                            ->columnSpan(3),

                        Forms\Components\FileUpload::make('promo_section.mainImage.path')
                            ->required()
                            ->columnSpan(1)
                            ->hiddenLabel()
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->imagePreviewHeight('300'),
                    ]),
                    Forms\Components\Section::make(__('filament/pages/edit-home-page-info.sections.slider'))->schema([
                        Forms\Components\Repeater::make('promo_section.slider')
                            ->hiddenLabel()
                            ->reorderable()
                            ->reorderableWithDragAndDrop()
                            ->grid(3)
                            ->schema([
                                Forms\Components\FileUpload::make('path')
                                    ->required()
                                    ->image()
                                    ->imageEditor()
                                    ->imagePreviewHeight('300'),
                            ]),
                    ])->collapsed(),
                ])->collapsed(),
            ])
            ->statePath('data');
    }

    protected function getRecordModel(): HomePageInfo
    {
        return HomePageInfo::getFirstRecord();
    }
}
