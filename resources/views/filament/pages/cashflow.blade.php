<x-filament-panels::page>

    <div>
        {{ $this->filtersForm }}
    </div>

    @livewire(\App\Filament\Widgets\Statistic\Bills::class)
    @livewire(\App\Filament\Widgets\Statistic\ExpensesGraph::class)
    @livewire(\App\Filament\Widgets\Statistic\NetCashflowChart::class)

    <div x-data="{ tab: 'expenses' }">
        <x-filament::tabs>
            <x-filament::tabs.item @click="tab = 'expenses'" :alpine-active="'tab === \'expenses\''"
                    icon="heroicon-o-queue-list">
                {{ __('Expenses')}}
            </x-filament::tabs.item>

            <x-filament::tabs.item @click="tab = 'supplies'" :alpine-active="'tab === \'supplies\''"
                    icon="heroicon-o-queue-list">
                {{ __('Supplies')}}
            </x-filament::tabs.item>

        </x-filament::tabs>

        <div class="mt-2">

        </div>
    </div>

</x-filament-panels::page>