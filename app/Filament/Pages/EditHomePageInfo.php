<?php

namespace App\Filament\Pages;

use App\Models\HomePageInfo;
use CactusGalaxy\FilamentAstrotomic\Forms\Components\TranslatableTabs;
use CactusGalaxy\FilamentAstrotomic\TranslatableTab;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

/**
 * @property Form $form
 */
class EditHomePageInfo extends Page
{
    use InteractsWithForms;
    use HasUnsavedDataChangesAlert;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.edit-home-page-info';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('filament/navigation.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/pages/home-page.label');
    }

    public function getTitle(): Htmlable|string
    {
        return __('filament/pages/home-page.title');
    }

    public function mount(): void
    {
        $record = $this->getRecordModel();

        $data = $record->attributesToArray();

        $this->form->fill($data);
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $this->getRecordModel()->update($data);
        } catch (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();

            return;
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();

        $this->rememberData();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Головний баннер')->columns()->schema([
                    FileUpload::make('hero_section.image.path')
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
                            Grid::make()->schema([
                                TextInput::make("hero_section.textLeft.translations.{$tab->getLocale()}")
                                    ->maxLength(255)
                                    ->hiddenLabel()
                                    ->required(),
                                TextInput::make("hero_section.textRight.translations.{$tab->getLocale()}")
                                    ->maxLength(255)
                                    ->hiddenLabel()
                                    ->required(),
                            ]),
                            TextInput::make("hero_section.quote.translations.{$tab->getLocale()}")
                                ->maxLength(255)
                                ->hiddenLabel()
                                ->required(),
                        ]),
                ])->collapsed(),

                Section::make('Промо інформація')->schema([
                    Grid::make(4)->schema([
                        TranslatableTabs::make('Heading')
                            ->localeTabSchema(fn (TranslatableTab $tab) => [
                                TextInput::make("promo_section.title.translations.{$tab->getLocale()}")
                                    ->maxLength(255)
                                    ->hiddenLabel()
                                    ->required(),
                                RichEditor::make("promo_section.description.translations.{$tab->getLocale()}")
                                    ->maxLength(255)
                                    ->hiddenLabel()
                                    ->required(),
                            ])
                            ->columnSpan(3),

                        FileUpload::make('promo_section.mainImage.path')
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
                    Section::make('Слайдер')->schema([
                        Repeater::make('promo_section.slider')
                            ->hiddenLabel()
                            ->reorderable()
                            ->reorderableWithDragAndDrop()
                            ->grid(3)
                            ->schema([
                                FileUpload::make('path')
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

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    private function getRecordModel(): HomePageInfo
    {
        return HomePageInfo::getFirstRecord();
    }
}
