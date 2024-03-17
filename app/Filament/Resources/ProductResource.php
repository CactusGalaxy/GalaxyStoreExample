<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Traits\ResourceHelper;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use CactusGalaxy\FilamentAstrotomic\Forms\Components\TranslatableTabs;
use CactusGalaxy\FilamentAstrotomic\Resources\Concerns\ResourceTranslatable;
use CactusGalaxy\FilamentAstrotomic\TranslatableTab;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    use ResourceTranslatable;
    use ResourceHelper;

    protected static ?string $model = Product::class;

    protected static ?string $slug = 'products';

    protected static ?string $recordTitleAttribute = 'title:uk';

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?int $navigationSort = 2;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
                            ->helperText('Генерується автоматично при зміні назви')
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

                                RichEditor::make($tab->makeName('description')),

                                Section::make(__('admin_labels.attributes.meta_fields'))
                                    ->hidden()
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
                        Select::make('category_id')
                            ->relationship('category')
                            ->searchable()
                            ->options(
                                Category::query()
                                    ->joinTranslations()
                                    ->pluck('title', 'categories.id')
                                    ->toArray()
                            )
                            ->required(),

                        Toggle::make('status')
                            ->default(true),
                    ]),

                    Section::make()->schema([
                        Placeholder::make('created_at')
                            ->content(fn (?Model $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Placeholder::make('updated_at')
                            ->content(fn (?Model $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ]),
                ])->columnSpan(['lg' => 1]),

                Section::make('product_card')
                    ->heading('Катка товару')
                    ->schema([
                        Grid::make()->schema([
                            TextInput::make('sku')
                                ->numeric()
                                ->required(),

                            TextInput::make('quantity')
                                ->required()
                                ->integer(),
                        ]),

                        Grid::make()->schema([
                            TextInput::make('price')
                                ->required()
                                ->minValue(0)
                                ->integer(),
                        ]),

                        TranslatableTabs::make('Heading')
                            ->localeTabSchema(fn (TranslatableTab $tab) => [
                                KeyValue::make($tab->makeName('characteristics'))
                                    ->label('Характеристики')
                                    ->addActionLabel('Додати')
                                    ->keyLabel('Параметр')
                                    ->valueLabel('Значення')
                                    ->reorderable(),
                            ]),
                    ]),

                Tabs::make('heading')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('slider')
                            ->label('Слайдер')
                            ->schema([
                                Repeater::make('sliders')
                                    ->hiddenLabel()
                                    ->required()
                                    ->minItems(2)
                                    ->defaultItems(2)
                                    ->reorderableWithButtons()
                                    ->schema([
                                        FileUpload::make('image')
                                            ->hiddenLabel()
                                            ->required()
                                            ->downloadable()
                                            ->disk('public')
                                            ->acceptedFileTypes(['image/*'])
                                            ->directory('products/images'),
                                    ]),
                            ]),
                        Tabs\Tab::make('attributes')
                            ->label('Атрибути')
                            ->schema([
                                Repeater::make('productAttributeValues')
                                    ->label('Атрибути')
                                    ->hiddenLabel()
                                    ->relationship()
                                    ->schema([
                                        Select::make('product_attribute_id')
                                            ->hiddenLabel()
                                            ->relationship('productAttribute')
                                            ->searchable()
                                            ->distinct()
                                            ->fixIndistinctState()
                                            ->options(
                                                ProductAttribute::query()
                                                    ->joinTranslations()
                                                    ->pluck('name', 'product_attributes.id')
                                                    ->toArray()
                                            )
                                            ->live(onBlur: true)
                                            ->required(),

                                        Select::make('attribute_value_id')
                                            ->hiddenLabel()
                                            ->relationship('attributeValue')
                                            ->visible(fn (Get $get) => $get('product_attribute_id'))
                                            ->searchable()
                                            ->allowHtml()
                                            ->options(function (Get $get) {
                                                $productAttributes = ProductAttribute::find(
                                                    $get('product_attribute_id')
                                                );

                                                $isColor = $productAttributes->display_type_in_card->isColor();

                                                $values = AttributeValue::query()
                                                    ->withTranslation()
                                                    ->where('product_attribute_id', $get('product_attribute_id'))
                                                    ->get();

                                                if ($isColor) {
                                                    return $values->mapWithKeys(
                                                        fn (AttributeValue $attributeValue) => [
                                                            $attributeValue->id => "<span style='background-color: {$attributeValue->value}'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  {$attributeValue->name}",
                                                        ]
                                                    )->toArray();
                                                }

                                                return $values->pluck('name', 'id')->toArray();
                                            })
                                            ->required(),
                                    ])
                                    ->columns(2),
                            ]),
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.translation.title')
                    ->label(__('filament/resources/products.attributes.category_id')),

                TextColumn::make('translation.title'),

                TextColumn::make('price'),

                ToggleColumn::make('status'),
            ])->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])->filters([
                SelectFilter::make('category_id')
                    ->label(__('filament/resources/products.attributes.category_id'))
                    ->searchable()
                    ->options(
                        Category::query()
                            ->joinTranslations()
                            ->pluck('title', 'categories.id')
                            ->toArray()
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
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
            'category.translations',
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['translations.title'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->category) {
            $key = __('filament/resources/products.attributes.category_id');
            $details[$key] = $record->category->title;
        }

        return $details;
    }
}
