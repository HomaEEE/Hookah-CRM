<x-filament-panels::page>

<div x-data="{ tab: 'expenses' }">
        <x-filament::tabs>
            <x-filament::tabs.item @click="tab = 'expenses'" :alpine-active="'tab === \'expenses\''"
                    icon="heroicon-o-queue-list">
                {{ __('Expenses')}}
{{--
                <x-slot name="badge">
                </x-slot> --}}
            </x-filament::tabs.item>


            <x-filament::tabs.item @click="tab = 'paying'" :alpine-active="'tab === \'paying\''"
                icon="heroicon-o-banknotes">
                {{ __('Paying')}}

                  {{-- <x-slot name="badge">
                </x-slot> --}}
            </x-filament::tabs.item>


            <x-filament::tabs.item @click="tab = 'booking'" :alpine-active="'tab === \'booking\''"
                icon="heroicon-o-bookmark-square">
                {{ __('Bookings')}}
            </x-filament::tabs.item>

            <x-filament::tabs.item @click="tab = 'menu'" :alpine-active="'tab === \'menu\''"
            icon="heroicon-o-briefcase">
                {{ __('Stop List')}}
            </x-filament::tabs.item>

            {{-- <x-filament::tabs.item @click="tab = 'tab5'" :alpine-active="'tab === \'tab5\''"
            icon="heroicon-o-calendar-days">
                {{ __('Events')}}
            </x-filament::tabs.item> --}}

        </x-filament::tabs>

        <div class="mt-2">
            <div x-show="tab === 'expenses'">
                @livewire(\App\Livewire\Dashboard\ExpensesDay::class)
            </div>
            <div x-show="tab === 'paying'">
                @livewire(\App\Livewire\Dashboard\Paying::class)
            </div>
            <div x-show="tab === 'booking'">
                @livewire(\App\Livewire\Dashboard\Bookings::class)
            </div>

            <div x-show="tab === 'menu'">
                @livewire(\App\Livewire\Dashboard\StopList::class)
            </div>

             {{-- <div x-show="tab === 'tab4'">
                @livewire(\App\Livewire\Tasks::class)
            </div>
            <div x-show="tab === 'tab5'">
                @livewire(\App\Livewire\EventTable::class)
            </div> --}}

        </div>
    </div>

</x-filament-panels::page>