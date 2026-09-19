<?php

namespace Modules\Manage\Models;

use App\Casts\MoneyCast;
use App\Models\Place;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Manage\Enums\OwnEnum;
use Modules\Manage\Enums\StateEnum;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class InventoryItem extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'warehouse_id',
        'name',
        'serial_number',
        'quantity',
        'price',
        'comment',
        'sold_quantity',
        'sold_price',
    ];

    protected $casts = [
        'own' => OwnEnum::class,
        'state' => StateEnum::class,
        'quantity' => 'integer',
        'price' => MoneyCast::class,
        'sold_quantity' => 'integer',
        'sold_price' => MoneyCast::class,
        'serial_number' => 'array',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function sales()
    {
        return $this->hasMany(InventorySale::class);
    }

    public function debtor()
    {
        return $this->belongsTo(Debtor::class);
    }

    public function getSerialNumbersList(): array
    {
        $sns = $this->serial_number;
        if (is_array($sns)) {
            return array_values(array_filter(array_map('trim', $sns)));
        }
        if (is_string($sns) && trim($sns) !== '') {
            return array_values(array_filter(array_map('trim', explode(',', $sns))));
        }

        return [];
    }

    public function recordSale(
        int $quantity,
        float|int $price,
        ?array $serialNumbers = null,
        ?string $notes = null,
        ?int $userId = null,
        bool $isDebt = false,
        ?string $debtorName = null,
        float|int $paidAmount = 0,
        float|int $debtAmount = 0,
        ?int $debtorId = null,
    ): InventorySale {
        if ($quantity > $this->quantity) {
            throw new \InvalidArgumentException("Количество для продажи ({$quantity}) превышает доступный остаток ({$this->quantity}).");
        }

        $totalPrice = $quantity * $price;
        if (! $isDebt) {
            $paidAmount = $totalPrice;
            $debtAmount = 0;
            $isDebtSettled = true;
            $settledAt = now();
        } else {
            $debtAmount = max(0, round($totalPrice - $paidAmount, 2));
            $isDebtSettled = $debtAmount <= 0;
            $settledAt = $isDebtSettled ? now() : null;
        }

        $sale = $this->sales()->create([
            'user_id' => $userId,
            'quantity' => $quantity,
            'price' => $price,
            'serial_numbers' => ! empty($serialNumbers) ? array_values($serialNumbers) : null,
            'notes' => $notes,
            'is_debt' => $isDebt,
            'debtor_id' => $isDebt ? $debtorId : null,
            'debtor_name' => $isDebt ? $debtorName : null,
            'paid_amount' => $paidAmount,
            'debt_amount' => $debtAmount,
            'is_debt_settled' => $isDebtSettled,
            'settled_at' => $settledAt,
        ]);

        $newQuantity = max(0, $this->quantity - $quantity);
        $this->quantity = $newQuantity;

        if (! empty($serialNumbers)) {
            $currentSns = $this->getSerialNumbersList();
            $remainingSns = array_values(array_diff($currentSns, $serialNumbers));
            $this->serial_number = $remainingSns;
        }

        if ($newQuantity === 0) {
            $this->state = StateEnum::Sold;
        }

        unset($this->debtor_id);
        $this->save();

        return $sale;
    }

    protected function totalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity * $this->price,
        );
    }

    protected function totalSoldQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('sales')
                ? (int) $this->sales->sum('quantity')
                : (int) $this->sales()->sum('quantity'),
        );
    }

    protected function soldTotalPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('sales')) {
                    return $this->sales->sum(fn ($sale) => $sale->quantity * $sale->price);
                }

                return round((DB::table('inventory_sales')
                    ->where('inventory_item_id', $this->id)
                    ->sum(DB::raw('quantity * price')) ?? 0) / 100, 2);
            },
        );
    }

    protected function totalUnpaidDebt(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('sales')) {
                    return $this->sales
                        ->where('is_debt', true)
                        ->where('is_debt_settled', false)
                        ->sum(fn ($sale) => (float) $sale->debt_amount);
                }

                return round((DB::table('inventory_sales')
                    ->where('inventory_item_id', $this->id)
                    ->where('is_debt', true)
                    ->where('is_debt_settled', false)
                    ->sum('debt_amount') ?? 0) / 100, 2);
            },
        );
    }
}
