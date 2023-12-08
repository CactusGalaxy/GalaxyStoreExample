<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Traits\ResourceHelper;
use App\Models\Category;
use CactusGalaxy\FilamentAstrotomic\Forms\Components\TranslatableTabs;
use CactusGalaxy\FilamentAstrotomic\Resources\Concerns\ResourceTranslatable;
use CactusGalaxy\FilamentAstrotomic\TranslatableTab;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CategoryResource extends Resource
{
    use ResourceTranslatable;
    use ResourceHelper;

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
            Grid::make(3)->schema([
                Grid::make(1)
                    ->schema([
                        TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->readOnly()
                            ->maxLength(255),

                        TranslatableTabs::make('Heading')
                            ->localeTabSchema(fn (TranslatableTab $tab) => [
                                TextInput::make($tab->makeName('title'))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) use ($tab) {
                                        if ($tab->isMainLocale()) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),

                                Section::make(__('admin_labels.attributes.meta_fields'))
                                    ->schema([
                                        TextInput::make($tab->makeName('meta_title'))
                                            ->maxLength(255),
                                        TextInput::make($tab->makeName('meta_keywords'))
                                            ->maxLength(255),
                                        RichEditor::make($tab->makeName('meta_description')),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Grid::make(1)->schema([
                    Section::make(__('admin_labels.tabs.general'))->schema([
                        TextInput::make('position')
                            ->required()
                            ->default(self::getModel()::max('position') + 1)
                            ->numeric(),

                        Toggle::make('status')
                            ->default(true),

                        FileUpload::make("image")
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

                    Section::make()->schema([
                        Placeholder::make('created_at')
                            ->content(fn (?Model $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Placeholder::make('updated_at')
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
                TextColumn::make('id')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('translation.title'),

                ImageColumn::make('image'),

                TextInputColumn::make('position')
                    ->toggleable(),

                ToggleColumn::make('status')
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
