<x-filament-panels::page>

    <div>
        {{-- {{ $this->filtersForm }} --}}
    </div>

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
                @livewire(\App\Livewire\Statistic\Expenses::class)
            </div>

            <div x-show="tab === 'supplies'">
                @livewire(\App\Livewire\Statistic\Supplies::class)
            </div>
        </div>
    </div>

</x-filament-panels::page>