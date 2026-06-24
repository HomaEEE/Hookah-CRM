<?php

namespace Tests\Feature\Filament;

use App\Models\Place;
use App\Models\User;
use Livewire\Livewire;
use Modules\Manage\Filament\Admin\Resources\Inventories\Pages\ManageInventory;
use Tests\TestCase;

class InventoryResourceTest extends TestCase
{
    public function test_can_render_inventory_resource_page(): void
    {
        $user = User::factory()->create();
        $place = Place::create([
            'name' => 'Test Place',
            'city' => 'Test City',
            'address' => 'Test Address',
        ]);
        $user->places()->attach($place);

        $this->actingAs($user);

        Livewire::test(ManageInventory::class, [
            'tenant' => $place->id,
        ])->assertSuccessful();
    }
}
