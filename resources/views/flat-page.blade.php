<x-filament-panels::page>
    <div wire:key="form-{{ $activeLocale }}">
        <x-filament-panels::form wire:submit="update">
            {{ $this->form }}
            <x-filament-panels::form.actions
                :actions="$this->getFormActions()"
            />
        </x-filament-panels::form>
    </div>
</x-filament-panels::page>
