<x-filament::widget>
    <x-filament::card>
        <form wire:submit.prevent="submit">
            {{ $this->form }}

            <button type="submit">
            </button>
        </form>
    </x-filament::card>
</x-filament::widget>