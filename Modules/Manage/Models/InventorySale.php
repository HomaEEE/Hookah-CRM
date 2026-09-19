<?php

namespace Modules\Manage\Models;

use App\Casts\MoneyCast;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventorySale extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'user_id',
        'quantity',
        'price',
        'serial_numbers',
        'notes',
        'is_debt',
        'debtor_id',
        'debtor_name',
        'paid_amount',
        'debt_amount',
        'is_debt_settled',
        'settled_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => MoneyCast::class,
        'paid_amount' => MoneyCast::class,
        'debt_amount' => MoneyCast::class,
        'is_debt' => 'boolean',
        'is_debt_settled' => 'boolean',
        'settled_at' => 'datetime',
        'serial_numbers' => 'array',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function debtor(): BelongsTo
    {
        return $this->belongsTo(Debtor::class);
    }

    protected function totalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity * $this->price,
        );
    }

    public function settlePayment(float|int $amount, ?string $note = null): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Сумма платежа должна быть больше нуля.');
        }

        $currentDebt = (float) $this->debt_amount;
        if ($amount > $currentDebt) {
            throw new \InvalidArgumentException("Сумма платежа ({$amount} UAH) превышает остаток долга ({$currentDebt} UAH).");
        }

        $newPaid = round((float) $this->paid_amount + $amount, 2);
        $newDebt = max(0, round($currentDebt - $amount, 2));
        $isSettled = $newDebt <= 0;

        $noteUpdate = $note ? " [Оплата: +{$amount} UAH ({$note})]" : " [Оплата: +{$amount} UAH]";

        $this->update([
            'paid_amount' => $newPaid,
            'debt_amount' => $newDebt,
            'is_debt_settled' => $isSettled,
            'settled_at' => $isSettled ? now() : null,
            'notes' => trim(($this->notes ?? '').$noteUpdate),
        ]);
    }

    public function settleFullPayment(?string $note = null): void
    {
        $this->settlePayment((float) $this->debt_amount, $note);
    }
}
