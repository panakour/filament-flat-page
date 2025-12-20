<x-filament-panels::page>
    <form wire:submit="update">
        {{ $this->form }}
        <x-filament::actions
            :actions="$this->getFormActions()"
        />
    </form>
</x-filament-panels::page>
