<?php

namespace App\Filament\Resources;

use App\Enums\ProductAttribute\DisplayType;
use App\Filament\Concerns\ConfigureResourceLabel;
use App\Filament\Resources\ProductAttributeResource\Pages;
use App\Models\ProductAttribute;
use CactusGalaxy\FilamentAstrotomic\Forms\Components\TranslatableTabs;
use CactusGalaxy\FilamentAstrotomic\Resources\Concerns\ResourceTranslatable;
use CactusGalaxy\FilamentAstrotomic\TranslatableTab;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductAttributeResource extends Resource
{
    use ResourceTranslatable;
    use ConfigureResourceLabel;

    protected static ?string $model = ProductAttribute::class;

    protected static ?string $slug = 'product-attributes';

    protected static ?string $recordTitleAttribute = 'name:uk';

    protected static ?string $navigationIcon = 'heroicon-o-bookmark-square';

    protected static ?int $navigationSort = 3;

    protected static bool $isGloballySearchable = false;

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
                        TranslatableTabs::make('Heading')
                            ->localeTabSchema(fn (TranslatableTab $tab) => [
                                Forms\Components\TextInput::make($tab->makeName('name'))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) use ($tab) {
                                        return $operation === 'create' && $tab->isMainLocale()
                                            ? $set('key', Str::slug($state))
                                            : null;
                                    }),
                            ]),

                        Forms\Components\Section::make()->schema([
                            Forms\Components\Select::make('display_type_in_card')
                                ->required()
                                ->live(onBlur: true)
                                ->options(DisplayType::getOptions()),

                            Forms\Components\TextInput::make('key')
                                ->required()
                                ->unique(ignoreRecord: true),
                        ])->hiddenOn('create'),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Grid::make(1)->schema([
                    Forms\Components\Section::make()->schema([
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

            Forms\Components\Section::make(__('Значення'))
                ->collapsible()
                ->collapsed(fn (string $operation) => $operation !== 'create')
                ->schema([
                    Forms\Components\Repeater::make('attributeValues')
                        ->hiddenLabel()
                        ->addActionLabel('Додати новий атрибут')
                        ->relationship()
                        ->collapsed()
                        ->collapsible()
                        ->itemLabel(function (array $state, Forms\Get $get) {
                            $name = $state['uk']['name'] ?? null;
                            $value = $state['uk']['value'] ?? null;

                            $displayType = $get('display_type_in_card');
                            // if parent record is color
                            if ($displayType === 'color') {
                                return Str::of(
                                    "<span style='background-color: {$value}'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> {$name}"
                                )->toHtmlString();
                            }

                            return Str::of("<span>{$name}</span>")->toHtmlString();
                        })
                        ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
                        ->schema([
                            TranslatableTabs::make()
                                ->localeTabSchema(fn (TranslatableTab $tab) => function (Forms\Get $get) use ($tab) {
                                    $inputs = [
                                        Forms\Components\TextInput::make($tab->makeName('name'))
                                            ->columnSpan(1)
                                            ->live(onBlur: true)
                                            ->required()
                                            ->maxLength(255),
                                    ];

                                    $displayType = $get('../../display_type_in_card');
                                    // if parent record is color
                                    if ($displayType === 'color') {
                                        $inputs[] = Forms\Components\ColorPicker::make($tab->makeName('value'))
                                            ->columnSpan(1)
                                            ->label(__('admin_labels.product_attributes.display_types.color'))
                                            ->required();
                                    }

                                    return [
                                        Forms\Components\Grid::make()->columns(2)->schema($inputs),
                                    ];
                                }),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction('edit')
            ->columns([
                Tables\Columns\TextColumn::make('translation.name'),
            ])->actions(actions: [
                // todo: do not use slide over with multiple translatable Forms\Components\tabs (value repeater)
                EditAction::make()/*
                    ->slideOver()
                    ->stickyModalHeader()*/,
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductAttributes::route('/'),
            'create' => Pages\CreateProductAttribute::route('/create'),
            'edit' => Pages\EditProductAttribute::route('/{record}/edit'),
        ];
    }
}
