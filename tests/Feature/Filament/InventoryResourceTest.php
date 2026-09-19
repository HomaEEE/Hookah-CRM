<?php

namespace Tests\Feature\Filament;

use App\Models\Place;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Modules\Manage\Enums\OwnEnum;
use Modules\Manage\Enums\StateEnum;
use Modules\Manage\Filament\Admin\Resources\Inventory\Pages\ManageInventory;
use Modules\Manage\Filament\Admin\Resources\Inventory\Widgets\InventoryStatsWidget;
use Modules\Manage\Models\Debtor;
use Modules\Manage\Models\InventoryItem;
use Modules\Manage\Models\InventorySale;
use Modules\Manage\Models\Warehouse;
use Tests\TestCase;

class InventoryResourceTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    private Place $place;

    private Warehouse $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->place = Place::create([
            'name' => 'Test Place',
            'city' => 'Test City',
            'address' => 'Test Address',
        ]);
        $this->user->places()->attach($this->place);

        $this->warehouse = Warehouse::create([
            'name' => 'Основной склад',
        ]);

        $this->actingAs($this->user);
    }

    public function test_can_render_inventory_resource_page(): void
    {
        Livewire::test(ManageInventory::class, [
            'tenant' => $this->place->id,
        ])->assertSuccessful();
    }

    public function test_partial_sale_decreases_quantity_and_keeps_active_state(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Кальян Alpha Hookah',
            'quantity' => 3,
            'price' => 1500,
            'serial_number' => ['SN-001', 'SN-002', 'SN-003'],
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        $sale = $item->recordSale(
            quantity: 1,
            price: 1800,
            serialNumbers: ['SN-001'],
            notes: 'Частичная продажа первой штуки',
            userId: $this->user->id,
        );

        $this->assertInstanceOf(InventorySale::class, $sale);
        $this->assertDatabaseHas('inventory_sales', [
            'id' => $sale->id,
            'inventory_item_id' => $item->id,
            'quantity' => 1,
            'price' => 180000, // MoneyCast sets in cents
            'notes' => 'Частичная продажа первой штуки',
        ]);

        $item->refresh();

        // Quantity reduced by 1
        $this->assertEquals(2, $item->quantity);
        // State must NOT be Sold because quantity > 0
        $this->assertEquals(StateEnum::ToSell, $item->state);
        $this->assertNotEquals(StateEnum::Sold, $item->state);
        // Sold serial number SN-001 removed from remaining list
        $this->assertEquals(['SN-002', 'SN-003'], $item->getSerialNumbersList());
        $this->assertEquals(1, $item->total_sold_quantity);
    }

    public function test_complete_sale_transitions_state_to_sold_when_quantity_reaches_zero(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Мундштук Darkside',
            'quantity' => 2,
            'price' => 200,
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        // Sell all 2 items
        $item->recordSale(
            quantity: 2,
            price: 250,
            notes: 'Продали всё',
            userId: $this->user->id,
        );

        $item->refresh();

        $this->assertEquals(0, $item->quantity);
        $this->assertEquals(StateEnum::Sold, $item->state);
        $this->assertEquals(2, $item->total_sold_quantity);
        $this->assertEquals(500, $item->soldTotalPrice);
    }

    public function test_selling_more_than_available_quantity_throws_exception(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Чаша Upgrade',
            'quantity' => 1,
            'price' => 300,
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $item->recordSale(
            quantity: 2,
            price: 350,
        );
    }

    public function test_filament_sell_action_executes_successfully(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Кальян Matt Pear',
            'quantity' => 5,
            'price' => 2000,
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        Livewire::test(ManageInventory::class, [
            'tenant' => $this->place->id,
        ])
            ->callAction(TestAction::make('sell')->table($item), [
                'sold_quantity' => 2,
                'sold_price' => 2200,
                'notes' => 'Продано 2 шт через Filament Action',
            ])
            ->assertNotified()
            ->assertHasNoFormErrors();

        $item->refresh();

        // 5 - 2 = 3 in stock
        $this->assertEquals(3, $item->quantity);
        // Not sold yet!
        $this->assertEquals(StateEnum::ToSell, $item->state);

        $this->assertDatabaseHas('inventory_sales', [
            'inventory_item_id' => $item->id,
            'quantity' => 2,
            'price' => 220000,
        ]);
    }

    public function test_filament_sell_action_with_serial_numbers(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Колба Drop',
            'quantity' => 2,
            'price' => 800,
            'serial_number' => ['SN-AAA', 'SN-BBB'],
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        Livewire::test(ManageInventory::class, [
            'tenant' => $this->place->id,
        ])
            ->callAction(TestAction::make('sell')->table($item), [
                'serial_numbers' => ['SN-AAA'],
                'sold_quantity' => 1,
                'sold_price' => 950,
            ])
            ->assertNotified()
            ->assertHasNoFormErrors();

        $item->refresh();

        $this->assertEquals(1, $item->quantity);
        $this->assertEquals(StateEnum::ToSell, $item->state);
        $this->assertEquals(['SN-BBB'], $item->getSerialNumbersList());

        $this->assertDatabaseHas('inventory_sales', [
            'inventory_item_id' => $item->id,
            'quantity' => 1,
            'price' => 95000,
        ]);

        $sale = $item->sales()->first();
        $this->assertEquals(['SN-AAA'], $sale->serial_numbers);
    }

    public function test_filament_sell_action_transitions_to_sold_when_depleted(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Шланг Soft Touch',
            'quantity' => 1,
            'price' => 300,
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        Livewire::test(ManageInventory::class, [
            'tenant' => $this->place->id,
        ])
            ->callAction(TestAction::make('sell')->table($item), [
                'sold_quantity' => 1,
                'sold_price' => 350,
            ])
            ->assertNotified()
            ->assertHasNoFormErrors();

        $item->refresh();

        $this->assertEquals(0, $item->quantity);
        $this->assertEquals(StateEnum::Sold, $item->state);
    }

    public function test_can_mount_sales_history_action(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Кальян с историей',
            'quantity' => 5,
            'price' => 1000,
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        $item->recordSale(quantity: 1, price: 1200, notes: 'Продажа 1');
        $item->recordSale(quantity: 2, price: 1100, notes: 'Продажа 2');

        Livewire::test(ManageInventory::class, [
            'tenant' => $this->place->id,
        ])
            ->mountAction(TestAction::make('salesHistory')->table($item))
            ->assertActionMounted(TestAction::make('salesHistory')->table($item));
    }

    public function test_inventory_stats_widget_computes_sales_revenue_correctly(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Щипцы Blade',
            'quantity' => 10,
            'price' => 150,
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        // Sale 1: 3 pcs @ 200 UAH = 600 UAH (full payment)
        $item->recordSale(quantity: 3, price: 200);

        // Sale 2: 2 pcs @ 250 UAH = 500 UAH (debt: paid 200, debt 300)
        $item->recordSale(
            quantity: 2,
            price: 250,
            isDebt: true,
            debtorName: 'Иван',
            paidAmount: 200,
        );

        Livewire::test(InventoryStatsWidget::class)
            ->assertSuccessful();

        $this->assertEquals(5, $item->total_sold_quantity);
        $this->assertEquals(1100, $item->soldTotalPrice);
        $this->assertEquals(5, $item->quantity);
        $this->assertEquals(300, $item->total_unpaid_debt);
    }

    public function test_sale_on_debt_with_partial_payment(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Кальян Alpha Hookah',
            'quantity' => 5,
            'price' => 2000,
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        // Sell 2 pcs @ 2500 UAH = 5000 UAH total. Partial payment 2000 UAH. Debt = 3000 UAH.
        $sale = $item->recordSale(
            quantity: 2,
            price: 2500,
            notes: 'Продажа в долг постоянному клиенту',
            userId: $this->user->id,
            isDebt: true,
            debtorName: 'Иван Петров (+380991234567)',
            paidAmount: 2000,
        );

        $this->assertInstanceOf(InventorySale::class, $sale);
        $this->assertTrue($sale->is_debt);
        $this->assertEquals('Иван Петров (+380991234567)', $sale->debtor_name);
        $this->assertEquals(2000, $sale->paid_amount);
        $this->assertEquals(3000, $sale->debt_amount);
        $this->assertFalse($sale->is_debt_settled);
        $this->assertNull($sale->settled_at);

        $item->refresh();
        $this->assertEquals(3, $item->quantity);
        $this->assertEquals(3000, $item->total_unpaid_debt);
    }

    public function test_debt_repayment_updates_amounts_and_settles(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Чаша Solaris',
            'quantity' => 3,
            'price' => 300,
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        $sale = $item->recordSale(
            quantity: 1,
            price: 350,
            userId: $this->user->id,
            isDebt: true,
            debtorName: 'Алексей',
            paidAmount: 100,
        );

        $this->assertEquals(250, $sale->debt_amount);
        $this->assertFalse($sale->is_debt_settled);

        // First partial repayment: 150 UAH
        $sale->settlePayment(150, 'Второй транш');
        $sale->refresh();

        $this->assertEquals(250, $sale->paid_amount);
        $this->assertEquals(100, $sale->debt_amount);
        $this->assertFalse($sale->is_debt_settled);
        $this->assertStringContainsString('Второй транш', $sale->notes);

        // Final repayment: remaining 100 UAH
        $sale->settlePayment(100, 'Полный расчет');
        $sale->refresh();

        $this->assertEquals(350, $sale->paid_amount);
        $this->assertEquals(0, $sale->debt_amount);
        $this->assertTrue($sale->is_debt_settled);
        $this->assertNotNull($sale->settled_at);

        $item->refresh();
        $this->assertEquals(0, $item->total_unpaid_debt);
    }

    public function test_filament_sell_action_with_debt_and_partial_payment(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Табак Tangiers 250g',
            'quantity' => 4,
            'price' => 1200,
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        $debtor = Debtor::create([
            'name' => 'Виталий (VIP)',
        ]);

        Livewire::test(ManageInventory::class, [
            'tenant' => $this->place->id,
        ])
            ->callAction(TestAction::make('sell')->table($item), [
                'sold_quantity' => 2,
                'sold_price' => 1300,
                'is_debt' => true,
                'debtor_id' => $debtor->id,
                'paid_amount' => 1000,
                'notes' => 'Обещал вернуть в пятницу',
            ])
            ->assertNotified()
            ->assertHasNoFormErrors();

        $item->refresh();
        $this->assertEquals(2, $item->quantity);

        $sale = $item->sales()->latest('id')->first();
        $this->assertNotNull($sale);
        $this->assertTrue($sale->is_debt);
        $this->assertEquals('Виталий (VIP)', $sale->debtor_name);
        $this->assertEquals($debtor->id, $sale->debtor_id);
        $this->assertEquals(1000, $sale->paid_amount);
        // Total = 2 * 1300 = 2600. Debt = 2600 - 1000 = 1600.
        $this->assertEquals(1600, $sale->debt_amount);
        $this->assertFalse($sale->is_debt_settled);
    }

    public function test_filament_settle_debt_action_partial(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Кальян с долгом',
            'quantity' => 2,
            'price' => 1000,
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        $sale = $item->recordSale(
            quantity: 1,
            price: 1000,
            userId: $this->user->id,
            isDebt: true,
            debtorName: 'Клиент 1',
            paidAmount: 200,
        );
        // Debt = 800

        Livewire::test(ManageInventory::class, [
            'tenant' => $this->place->id,
        ])
            ->callAction(TestAction::make('settleDebt')->table($item), [
                'inventory_sale_id' => $sale->id,
                'settlement_type' => 'partial',
                'amount' => 300,
                'notes' => 'Частичная доплата 300',
            ])
            ->assertNotified()
            ->assertHasNoFormErrors();

        $sale->refresh();
        $this->assertEquals(500, $sale->paid_amount);
        $this->assertEquals(500, $sale->debt_amount);
        $this->assertFalse($sale->is_debt_settled);
    }

    public function test_filament_settle_debt_action_full(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Кальян с полным закрытием',
            'quantity' => 2,
            'price' => 1000,
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        $sale = $item->recordSale(
            quantity: 1,
            price: 1000,
            userId: $this->user->id,
            isDebt: true,
            debtorName: 'Клиент 2',
            paidAmount: 400,
        );
        // Debt = 600

        Livewire::test(ManageInventory::class, [
            'tenant' => $this->place->id,
        ])
            ->callAction(TestAction::make('settleDebt')->table($item), [
                'inventory_sale_id' => $sale->id,
                'settlement_type' => 'full',
                'notes' => 'Полное закрытие долга',
            ])
            ->assertNotified()
            ->assertHasNoFormErrors();

        $sale->refresh();
        $this->assertEquals(1000, $sale->paid_amount);
        $this->assertEquals(0, $sale->debt_amount);
        $this->assertTrue($sale->is_debt_settled);
        $this->assertNotNull($sale->settled_at);

        $item->refresh();
        $this->assertEquals(0, $item->total_unpaid_debt);
    }

    public function test_filament_sell_action_quick_add_debtor(): void
    {
        $item = InventoryItem::create([
            'warehouse_id' => $this->warehouse->id,
            'place_id' => $this->place->id,
            'name' => 'Кальян с новым должником',
            'quantity' => 3,
            'price' => 1500,
            'state' => StateEnum::ToSell,
            'own' => OwnEnum::Own,
        ]);

        $debtor = Debtor::create([
            'name' => 'Новый Покупатель',
        ]);

        Livewire::test(ManageInventory::class, [
            'tenant' => $this->place->id,
        ])
            ->callAction(TestAction::make('sell')->table($item), [
                'sold_quantity' => 1,
                'sold_price' => 1500,
                'is_debt' => true,
                'debtor_id' => $debtor->id,
                'paid_amount' => 500,
                'notes' => 'Быстрое добавление должника',
            ])
            ->assertNotified()
            ->assertHasNoFormErrors();

        $sale = $item->sales()->latest('id')->first();
        $this->assertNotNull($sale);
        $this->assertEquals('Новый Покупатель', $sale->debtor_name);
        $this->assertEquals($debtor->id, $sale->debtor_id);
        $this->assertEquals(500, $sale->paid_amount);
        $this->assertEquals(1000, $sale->debt_amount);
    }
}
