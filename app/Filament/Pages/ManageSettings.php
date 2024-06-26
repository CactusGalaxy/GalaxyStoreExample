<?php

namespace App\Filament\Pages;

use App\Data\Localized;
use App\Settings\FooterSettings;
use App\Settings\SiteSettings;
use CactusGalaxy\FilamentAstrotomic\Forms\Components\TranslatableTabs;
use CactusGalaxy\FilamentAstrotomic\TranslatableTab;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

/**
 * @property Form $siteForm
 * @property Form $footerForm
 */
class ManageSettings extends Page
{
    use CanUseDatabaseTransactions;
    use InteractsWithForms;
    use HasUnsavedDataChangesAlert;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.settings';

    public ?array $siteData = [];
    public ?array $footerData = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/pages/settings.label');
    }

    public function getTitle(): Htmlable|string
    {
        return __('filament/pages/settings.title');
    }

    public function mount(): void
    {
        $this->fillForms();
    }

    protected function fillForms(): void
    {
        $this->callHook('beforeFill');

        $this->siteForm->fill(app(SiteSettings::class)->toArray());
        $this->footerForm->fill(app(FooterSettings::class)->toArray());

        $this->callHook('afterFill');
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $siteData = $this->siteForm->getState();
            $siteData['name'] = new Localized(...$siteData['name']);
            $siteData['description'] = new Localized(...$siteData['description']);

            $footerData = $this->footerForm->getState();
            $footerData['description'] = new Localized(...$footerData['description']);

            $settingsList = [
                SiteSettings::class => $siteData,
                FooterSettings::class => $footerData,
            ];

            foreach ($settingsList as $settingsClass => $data) {
                $settings = app($settingsClass);

                $settings->fill($data);
                $settings->save();
            }

            $this->callHook('afterSave');

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();

        $this->rememberData();
    }

    protected function getForms(): array
    {
        return [
            'siteForm',
            'footerForm',
        ];
    }

    public function siteForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament/pages/settings.site_settings'))->columns(3)->schema([
                    Forms\Components\FileUpload::make('logo')
                        ->required()
                        ->columnSpan(1)
                        ->label('Лого')
                        ->disk('public')
                        ->image()
                        ->imageEditor()
                        ->downloadable()
                        ->openable()
                        ->imagePreviewHeight('200'),

                    TranslatableTabs::make('Heading')
                        ->columnSpan(2)
                        ->localeTabSchema(fn (TranslatableTab $tab) => [
                            Forms\Components\TextInput::make("name.translations.{$tab->getLocale()}")
                                ->maxLength(255)
                                ->hiddenLabel()
                                ->required(),
                            Forms\Components\Textarea::make("description.translations.{$tab->getLocale()}")
                                ->maxLength(255)
                                ->hiddenLabel()
                                ->required(),
                        ]),
                ])->collapsible(),
            ])
            ->statePath('siteData');
    }

    public function footerForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament/pages/settings.footer_settings'))->schema([
                    TranslatableTabs::make('Heading')
                        ->localeTabSchema(fn (TranslatableTab $tab) => [
                            Forms\Components\Textarea::make("description.translations.{$tab->getLocale()}")
                                ->maxLength(255)
                                ->hiddenLabel()
                                ->required(),
                        ]),
                ])->collapsible(),
            ])
            ->statePath('footerData');
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
}
