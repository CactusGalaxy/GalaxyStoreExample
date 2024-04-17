<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\ConfigureResourceLabel;
use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CategoryResource extends Resource
{
    use ResourceTranslatable;
    use ConfigureResourceLabel;

    protected static ?string $model = Category::class;

    protected static ?string $slug = 'categories';

    protected static ?string $recordTitleAttribute = 'title:uk';

    protected static ?string $navigationIcon = 'bi-shop';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('filament/navigation.groups.shop');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->readOnly()
                            ->maxLength(255),

                        TranslatableTabs::make('Heading')
                            ->localeTabSchema(fn (TranslatableTab $tab) => [
                                Forms\Components\TextInput::make($tab->makeName('title'))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) use ($tab) {
                                        if ($tab->isMainLocale()) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),

                                Forms\Components\Section::make(__('admin_labels.attributes.meta_fields'))
                                    ->schema([
                                        Forms\Components\TextInput::make($tab->makeName('meta_title'))
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make($tab->makeName('meta_keywords'))
                                            ->maxLength(255),
                                        Forms\Components\RichEditor::make($tab->makeName('meta_description')),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Grid::make(1)->schema([
                    Forms\Components\Section::make(__('admin_labels.tabs.general'))->schema([
                        Forms\Components\TextInput::make('position')
                            ->required()
                            ->default(self::getModel()::max('position') + 1)
                            ->numeric(),

                        Forms\Components\Toggle::make('status')
                            ->default(true),

                        Forms\Components\FileUpload::make("image")
                            ->hiddenLabel()
                            ->columnSpan(1)
                            ->disk('public')
                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file) {
                                return 'uploads/categories/' . $file->getClientOriginalName();
                            })
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->imagePreviewHeight('300')
                            ->panelAspectRatio('2:1')
                            ->panelLayout('integrated'),
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
            ->defaultSort('id', 'desc')
            ->reorderable('position')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('translation.title'),

                Tables\Columns\ImageColumn::make('image'),

                Tables\Columns\TextInputColumn::make('position')
                    ->toggleable(),

                Tables\Columns\ToggleColumn::make('status')
                    ->toggleable(),
            ])->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultsLimit(): int
    {
        return 10;
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with([
            'translations',
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['translations.title'];
    }
}
