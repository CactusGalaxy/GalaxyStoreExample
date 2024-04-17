<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Concerns\ConfigureResourceLabel;
use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use CactusGalaxy\FilamentAstrotomic\Forms\Components\TranslatableTabs;
use CactusGalaxy\FilamentAstrotomic\Resources\Concerns\ResourceTranslatable;
use CactusGalaxy\FilamentAstrotomic\TranslatableTab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BannerResource extends Resource
{
    use ResourceTranslatable;
    use ConfigureResourceLabel;

    protected static ?string $model = Banner::class;

    protected static ?string $slug = 'banners';

    protected static ?string $recordTitleAttribute = 'title:uk';

    protected static ?string $navigationIcon = 'bi-image-fill';

    protected static bool $isGloballySearchable = false;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('filament/navigation.groups.content');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Grid::make(1)
                    ->schema([
                        TranslatableTabs::make('Heading')
                            ->localeTabSchema(fn (TranslatableTab $tab) => [
                                Forms\Components\TextInput::make($tab->makeName('title'))
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\RichEditor::make($tab->makeName('description'))
                                    ->maxLength(500),
                            ]),

                        Forms\Components\Section::make(__('admin_labels.attributes.image'))->schema([
                            Forms\Components\FileUpload::make('image')
                                ->required()
                                ->hiddenLabel()
                                ->acceptedFileTypes(['image/*'])
                                ->disk('public')
                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file) {
                                    return 'uploads/banners/' . $file->getClientOriginalName();
                                })
                                ->image()
                                ->imageEditor()
                                ->downloadable()
                                ->openable()
                                ->panelAspectRatio('16:9')
                                ->panelLayout('integrated'),
                        ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Grid::make(1)->schema([
                    Forms\Components\Section::make(__('admin_labels.tabs.general'))->schema([
                        Forms\Components\TextInput::make('position')
                            ->visibleOn('edit')
                            ->default(self::getModel()::max('position') + 1)
                            ->numeric(),

                        Forms\Components\Toggle::make('status')
                            ->default(true),
                    ]),

                    Forms\Components\Section::make()->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->content(fn (?Model $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Forms\Components\Placeholder::make('updated_at')
                            ->content(fn (?Model $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                Tables\Columns\TextColumn::make('translation.title'),

                Tables\Columns\ImageColumn::make('image'),

                Tables\Columns\TextInputColumn::make('position'),

                Tables\Columns\ToggleColumn::make('status'),
            ])->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
