<x-filament-panels::page>

    <div>
        {{ $this->filtersForm }}
    </div>

    @livewire(\Modules\Finance\Filament\Admin\Widgets\Statistic\Bills::class)
    @livewire(\Modules\Finance\Filament\Admin\Widgets\Statistic\ExpensesGraph::class)
    @livewire(\Modules\Finance\Filament\Admin\Widgets\Statistic\NetCashflowChart::class)

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
            <div x-show="tab === 'expenses'">
                @livewire(\Modules\Finance\Filament\Admin\Widgets\Statistic\Expenses::class, ['filters' => $this->filters ])
            </div>

            <div x-show="tab === 'supplies'">
                @livewire(\Modules\Finance\Filament\Admin\Widgets\Statistic\Supplies::class, ['filters' => $this->filters ])
            </div>
        </div>
    </div>

</x-filament-panels::page>