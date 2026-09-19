<?php

namespace Modules\Manage\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Modules\Manage\Models\Debtor;
use Modules\Manage\Models\InventoryItem;

class SellAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'sell';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Продать')
            ->icon(Heroicon::CurrencyDollar)
            ->color('success')
            ->visible(fn (InventoryItem $record): bool => $record->quantity > 0)
            ->schema([
                Select::make('serial_numbers')
                    ->label('Серийные номера (SN)')
                    ->options(function (InventoryItem $record): array {
                        $sns = $record->getSerialNumbersList();

                        return ! empty($sns) ? array_combine($sns, $sns) : [];
                    })
                    ->multiple()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?array $state): void {
                        if (! empty($state)) {
                            $set('sold_quantity', count($state));
                        }
                    })
                    ->visible(fn (InventoryItem $record): bool => ! empty($record->getSerialNumbersList()))
                    ->helperText('Выберите конкретные SN для продажи. Количество рассчитается автоматически.'),

                TextInput::make('sold_quantity')
                    ->label('Количество продано')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(fn (InventoryItem $record): int => $record->quantity)
                    ->required()
                    ->default(1)
                    ->disabled(fn (Get $get, InventoryItem $record): bool => ! empty($record->getSerialNumbersList()) && ! empty($get('serial_numbers')))
                    ->dehydrated()
                    ->live(onBlur: true)
                    ->helperText(fn (InventoryItem $record): string => "Доступно на складе: {$record->quantity} шт."),

                TextInput::make('sold_price')
                    ->label('Цена продажи за шт. (UAH)')
                    ->numeric()
                    ->required()
                    ->default(fn (InventoryItem $record) => $record->price)
                    ->live(onBlur: true),

                Toggle::make('is_debt')
                    ->label('Продажа в долг')
                    ->default(false)
                    ->live(),

                Select::make('debtor_id')
                    ->label('Покупатель / Должник')
                    ->relationship('debtor', 'name')
                    ->searchable()
                    ->quickAdd()
                    ->required(fn (Get $get): bool => (bool) $get('is_debt'))
                    ->visible(fn (Get $get): bool => (bool) $get('is_debt')),

                TextInput::make('paid_amount')
                    ->label('Частичный платеж / аванс (UAH)')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->visible(fn (Get $get): bool => (bool) $get('is_debt'))
                    ->live(onBlur: true)
                    ->helperText(function (Get $get, InventoryItem $record): string {
                        $qty = (int) ($get('sold_quantity') ?: 1);
                        $price = (float) ($get('sold_price') ?: $record->price);
                        $total = round($qty * $price, 2);
                        $paid = (float) ($get('paid_amount') ?: 0);
                        $debt = max(0, round($total - $paid, 2));

                        return "Общая сумма: {$total} UAH | Оплачено: {$paid} UAH | Остаток долга: {$debt} UAH";
                    }),

                Textarea::make('notes')
                    ->label('Примечание / Комментарий')
                    ->rows(2)
                    ->nullable(),
            ])
            ->action(function (InventoryItem $record, array $data): void {
                $serialNumbers = ! empty($data['serial_numbers']) ? array_values((array) $data['serial_numbers']) : null;
                $quantity = ! empty($serialNumbers) ? count($serialNumbers) : (int) $data['sold_quantity'];
                $price = (float) $data['sold_price'];
                $notes = $data['notes'] ?? null;
                $userId = auth()->id();
                $isDebt = (bool) ($data['is_debt'] ?? false);
                $debtorId = $isDebt ? ($data['debtor_id'] ?? null) : null;
                $debtorName = $debtorId ? Debtor::find($debtorId)?->name : null;
                $paidAmount = $isDebt ? (float) ($data['paid_amount'] ?? 0) : ($quantity * $price);
                $debtAmount = $isDebt ? max(0, round(($quantity * $price) - $paidAmount, 2)) : 0;

                $record->recordSale(
                    quantity: $quantity,
                    price: $price,
                    serialNumbers: $serialNumbers,
                    notes: $notes,
                    userId: $userId,
                    isDebt: $isDebt,
                    debtorName: $debtorName,
                    paidAmount: $paidAmount,
                    debtAmount: $debtAmount,
                    debtorId: $debtorId,
                );
            })
            ->successNotification(function (InventoryItem $record, array $data): Notification {
                $isDebt = (bool) ($data['is_debt'] ?? false);
                $debtorId = $data['debtor_id'] ?? null;
                $debtor = $debtorId ? (Debtor::find($debtorId)?->name ?? '') : '';
                $title = $isDebt ? "Продажа в долг оформлена ({$debtor})" : 'Продажа оформлена';
                $stockText = $record->quantity > 0
                    ? "Продажа зафиксирована. Текущий остаток: {$record->quantity} шт."
                    : 'Все единицы проданы. Статус изменен на «Продано».';

                return Notification::make()
                    ->success()
                    ->title($title)
                    ->body($stockText);
            });
    }
}
