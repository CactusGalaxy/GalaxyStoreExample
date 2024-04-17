<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\ConfigurePageLabel;
use CactusGalaxy\FilamentAstrotomic\Resources\Concerns\HasLocales;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * @property Form $form
 */
abstract class EditPage extends Page
{
    use ConfigurePageLabel;
    use InteractsWithForms;
    use HasLocales {
        HasLocales::getFilamentTranslatableContentDriver as localeGetFilamentTranslatableContentDriver;
    }
    use HasUnsavedDataChangesAlert;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.edit-page';

    public ?array $data = [];

    public function getFilamentTranslatableContentDriver(): ?string
    {
        return $this->localeGetFilamentTranslatableContentDriver();
    }

    public function mount(): void
    {
        $record = $this->getRecordModel();

        $data = $record->attributesToArray();

        $data = $this->mutateTranslatableData($record, $data);

        $this->form->fill($data);
    }

    public function save(): void
    {
        try {
            $this->resetErrorBag();

            $this->form->validate();

            $data = $this->form->getState();

            $this->getRecordModel()->update($data);
        } catch (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();

            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save')
                ->keyBindings(['command+s', 'ctrl+s', 'mod+s']),
        ];
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::Right;
    }

    abstract protected function getRecordModel(): Model;
}
