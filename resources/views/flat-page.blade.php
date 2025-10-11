<x-filament-panels::page>
    <div wire:key="form-{{ $activeLocale }}">
        <form wire:submit="update">
            {{ $this->form }}
            <x-filament::actions
                :actions="$this->getFormActions()"
            />
        </form>
    </div>
</x-filament-panels::page>
