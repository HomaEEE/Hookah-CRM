<x-filament-panels::page>

<div x-data="{ tab: 'tab1' }">
        <x-filament::tabs>
            <x-filament::tabs.item @click="tab = 'tab1'" :alpine-active="'tab === \'tab1\''"
                    icon="heroicon-o-queue-list">
                {{ __('Expenses')}}
            </x-filament::tabs.item>

            <x-filament::tabs.item @click="tab = 'tab2'" :alpine-active="'tab === \'tab2\''"
                icon="heroicon-o-bookmark-square">
                {{ __('Bookings')}}
            </x-filament::tabs.item>

            <x-filament::tabs.item @click="tab = 'tab3'" :alpine-active="'tab === \'tab3\''"
                icon="heroicon-o-banknotes">
                {{ __('Paying')}}
            </x-filament::tabs.item>

            <x-filament::tabs.item @click="tab = 'tab4'" :alpine-active="'tab === \'tab4\''"
            icon="heroicon-o-briefcase">
                {{ __('Tasks')}}
            </x-filament::tabs.item>

            <x-filament::tabs.item @click="tab = 'tab5'" :alpine-active="'tab === \'tab5\''"
            icon="heroicon-o-calendar-days">
                {{ __('Events')}}
            </x-filament::tabs.item>
        </x-filament::tabs>

        <div class="mt-2">
            <div x-show="tab === 'tab1'">
                @livewire(\App\Livewire\ExpensesDay::class)
            </div>
            <div x-show="tab === 'tab2'">
                @livewire(\App\Livewire\Bookings::class)
            </div>
             <div x-show="tab === 'tab3'">
                @livewire(\App\Livewire\Paying::class)
            </div>
             <div x-show="tab === 'tab4'">
                @livewire(\App\Livewire\Tasks::class)
            </div>
            <div x-show="tab === 'tab5'">
                @livewire(\App\Livewire\EventTable::class)
            </div>

        </div>
    </div>

</x-filament-panels::page>
