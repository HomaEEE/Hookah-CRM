<x-filament-panels::page>
    <div x-data="{ tab: 'tab1' }">
        <x-filament::tabs label="Content tabs">
            <x-filament::tabs.item @click="tab = 'tab1'" :alpine-active="'tab === \'tab1\''">
                Events
            </x-filament::tabs.item>

            <x-filament::tabs.item @click="tab = 'tab2'" :alpine-active="'tab === \'tab2\''">
                Events Role
            </x-filament::tabs.item>

        </x-filament::tabs>

        {{-- Display Events Resource Table --}}

        <div class="mt-2">
            <div x-show="tab === 'tab1'">
            @livewire(\App\Livewire\Dashboard\PostsChart::class)
            </div>
            <div x-show="tab === 'tab2'">
                <livewire:events.list-events-role/>  // your livewire view
            </div>
        </div>
    </div>
    {{-- Display Events Role Resource Table --}}
</x-filament-panels::page>