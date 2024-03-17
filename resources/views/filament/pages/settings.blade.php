<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->siteForm }}

        {{ $this->footerForm }}

        <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
        />
    </x-filament-panels::form>

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
