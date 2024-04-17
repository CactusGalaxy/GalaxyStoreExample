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
                $this->getMainBannerSection(),
                $this->getPromoSection(),
            ])
            ->statePath('data');
    }

    protected function getMainBannerSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make(__('filament/pages/edit-home-page-info.sections.main_banner'))
            ->columns()
            ->statePath('hero_section')
            ->schema([
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
                    ->makeNameUsing(fn ($name, $locale) => "{$name}.translations.{$locale}")
                    ->localeTabSchema(fn (TranslatableTab $tab) => [
                        Forms\Components\Grid::make()->schema([
                            Forms\Components\TextInput::make($tab->makeName('textLeft'))
                                ->maxLength(255)
                                ->hiddenLabel()
                                ->required(),
                            Forms\Components\TextInput::make($tab->makeName('textRight'))
                                ->maxLength(255)
                                ->hiddenLabel()
                                ->required(),
                        ]),
                        Forms\Components\TextInput::make($tab->makeName('quote'))
                            ->maxLength(255)
                            ->hiddenLabel()
                            ->required(),
                    ]),
            ])->collapsed();
    }

    protected function getPromoSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make(__('filament/pages/edit-home-page-info.sections.promo_info'))
            ->statePath('promo_section')
            ->schema([
                Forms\Components\Grid::make(4)->schema([
                    TranslatableTabs::make('Heading')
                        ->makeNameUsing(fn ($name, $locale) => "{$name}.translations.{$locale}")
                        ->localeTabSchema(fn (TranslatableTab $tab) => [
                            Forms\Components\TextInput::make($tab->makeName('title'))
                                ->maxLength(255)
                                ->hiddenLabel()
                                ->required(),
                            Forms\Components\RichEditor::make($tab->makeName('description'))
                                ->maxLength(255)
                                ->hiddenLabel()
                                ->required(),
                        ])
                        ->columnSpan(3),

                    Forms\Components\FileUpload::make('mainImage.path')
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
                    Forms\Components\Repeater::make('slider')
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
            ])->collapsed();
    }

    protected function getRecordModel(): HomePageInfo
    {
        return HomePageInfo::getFirstRecord();
    }
}
