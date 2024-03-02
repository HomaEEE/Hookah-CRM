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

        {{-- Display Events Resource Table --}}

        <div class="mt-2">
            <div x-show="tab === 'tab1'">
                <livewire:ExpensesDay/>
            </div>
            <div x-show="tab === 'tab2'">
                <livewire:Bookings/>
            </div>
             <div x-show="tab === 'tab3'">
                <livewire:Paying/>
            </div>
             <div x-show="tab === 'tab4'">
                <livewire:Tasks/>
            </div>
            <div x-show="tab === 'tab5'">
                <livewire:EventTable/>
            </div>

        </div>
    </div>


</x-filament-panels::page>
