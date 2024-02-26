<x-filament-widgets::widget>
       <x-filament::tabs label="Content tabs">
        <x-filament::tabs.item active>
            Tab 1
            @livewire(\App\Filament\Widdgets\Statistic\Bills::class)
        </x-filament::tabs.item>

        <x-filament::tabs.item>
            Tab 2
        </x-filament::tabs.item>

        <x-filament::tabs.item>
            Tab 2
        </x-filament::tabs.item>
    </x-filament::tabs>
</x-filament-widgets::widget>
