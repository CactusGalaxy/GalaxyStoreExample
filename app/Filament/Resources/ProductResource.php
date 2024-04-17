<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\ConfigureResourceLabel;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use CactusGalaxy\FilamentAstrotomic\Forms\Components\TranslatableTabs;
use CactusGalaxy\FilamentAstrotomic\Resources\Concerns\ResourceTranslatable;
use CactusGalaxy\FilamentAstrotomic\TranslatableTab;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    use ResourceTranslatable;
    use ConfigureResourceLabel;

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
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->readOnly()
                            ->helperText('Генерується автоматично при зміні назви')
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

                                Forms\Components\RichEditor::make($tab->makeName('description')),

                                Forms\Components\Section::make(__('admin_labels.attributes.meta_fields'))
                                    ->hidden()
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
                        Forms\Components\Select::make('category_id')
                            ->relationship('category')
                            ->searchable()
                            ->options(
                                Category::query()
                                    ->joinTranslations()
                                    ->pluck('title', 'categories.id')
                                    ->toArray()
                            )
                            ->required(),

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

                Forms\Components\Section::make('product_card')
                    ->heading('Катка товару')
                    ->schema([
                        Forms\Components\Grid::make()->schema([
                            Forms\Components\TextInput::make('sku')
                                ->numeric()
                                ->required(),

                            Forms\Components\TextInput::make('quantity')
                                ->required()
                                ->integer(),
                        ]),

                        Forms\Components\Grid::make()->schema([
                            Forms\Components\TextInput::make('price')
                                ->required()
                                ->minValue(0)
                                ->integer(),
                        ]),

                        TranslatableTabs::make('Heading')
                            ->localeTabSchema(fn (TranslatableTab $tab) => [
                                Forms\Components\KeyValue::make($tab->makeName('characteristics'))
                                    ->label('Характеристики')
                                    ->addActionLabel('Додати')
                                    ->keyLabel('Параметр')
                                    ->valueLabel('Значення')
                                    ->reorderable(),
                            ]),
                    ]),

                Forms\Components\Tabs::make('heading')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('slider')
                            ->label('Слайдер')
                            ->schema([
                                Forms\Components\Repeater::make('sliders')
                                    ->hiddenLabel()
                                    ->required()
                                    ->minItems(2)
                                    ->defaultItems(2)
                                    ->reorderableWithButtons()
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
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
                                Forms\Components\Repeater::make('productAttributeValues')
                                    ->label('Атрибути')
                                    ->hiddenLabel()
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Select::make('product_attribute_id')
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

                                        Forms\Components\Select::make('attribute_value_id')
                                            ->hiddenLabel()
                                            ->relationship('attributeValue')
                                            ->visible(fn (Forms\Get $get) => $get('product_attribute_id'))
                                            ->searchable()
                                            ->allowHtml()
                                            ->options(function (Forms\Get $get) {
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
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.translation.title')
                    ->label(__('filament/resources/products.attributes.category_id')),

                Tables\Columns\TextColumn::make('translation.title'),

                Tables\Columns\TextColumn::make('price'),

                Tables\Columns\ToggleColumn::make('status'),
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
