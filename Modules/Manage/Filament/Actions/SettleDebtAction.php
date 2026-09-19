<?php

namespace Modules\Manage\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Modules\Manage\Models\InventoryItem;
use Modules\Manage\Models\InventorySale;

class SettleDebtAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'settleDebt';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Погасить долг')
            ->icon(Heroicon::Banknotes)
            ->color('warning')
            ->visible(fn (InventoryItem $record): bool => $record->total_unpaid_debt > 0)
            ->modalHeading(fn (InventoryItem $record): string => "Погашение долга: {$record->name}")
            ->modalDescription('Внесение полного или частичного платежа по задолженности.')
            ->modalSubmitActionLabel('Внести платеж')
            ->schema([
                Select::make('inventory_sale_id')
                    ->label('Выберите продажу / должника')
                    ->options(function (InventoryItem $record): array {
                        return $record->sales()
                            ->where('is_debt', true)
                            ->where('is_debt_settled', false)
                            ->get()
                            ->mapWithKeys(fn (InventorySale $sale) => [
                                $sale->id => "{$sale->debtor_name} — Долг: {$sale->debt_amount} UAH (от {$sale->created_at->format('d.m.Y H:i')})",
                            ])
                            ->toArray();
                    })
                    ->default(function (InventoryItem $record): ?int {
                        return $record->sales()
                            ->where('is_debt', true)
                            ->where('is_debt_settled', false)
                            ->latest('id')
                            ->value('id');
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?int $state, Get $get): void {
                        if ($state) {
                            $sale = InventorySale::find($state);
                            if ($sale && $get('settlement_type') === 'full') {
                                $set('amount', $sale->debt_amount);
                            }
                        }
                    }),

                Radio::make('settlement_type')
                    ->label('Тип погашения')
                    ->options([
                        'full' => 'Полное погашение',
                        'partial' => 'Частичное погашение',
                    ])
                    ->default('full')
                    ->inline()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state, Get $get): void {
                        $saleId = $get('inventory_sale_id');
                        if ($saleId && $state === 'full') {
                            $sale = InventorySale::find($saleId);
                            if ($sale) {
                                $set('amount', $sale->debt_amount);
                            }
                        }
                    }),

                TextInput::make('amount')
                    ->label('Сумма платежа (UAH)')
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->default(function (Get $get, InventoryItem $record): ?float {
                        $saleId = $get('inventory_sale_id') ?? $record->sales()
                            ->where('is_debt', true)
                            ->where('is_debt_settled', false)
                            ->latest('id')
                            ->value('id');

                        if ($saleId) {
                            $sale = InventorySale::find($saleId);

                            return $sale ? (float) $sale->debt_amount : null;
                        }

                        return null;
                    })
                    ->disabled(fn (Get $get): bool => $get('settlement_type') === 'full')
                    ->dehydrated()
                    ->helperText(function (Get $get): ?string {
                        $saleId = $get('inventory_sale_id');
                        if (! $saleId) {
                            return null;
                        }
                        $sale = InventorySale::find($saleId);
                        if (! $sale) {
                            return null;
                        }

                        $amount = (float) ($get('amount') ?: 0);
                        $remaining = max(0, round($sale->debt_amount - $amount, 2));

                        return "Текущий долг: {$sale->debt_amount} UAH | После оплаты останется: {$remaining} UAH";
                    }),

                Textarea::make('notes')
                    ->label('Примечание к платежу')
                    ->rows(2)
                    ->placeholder('Например: Оплата наличными / перевод на карту')
                    ->nullable(),
            ])
            ->action(function (InventoryItem $record, array $data): void {
                $sale = InventorySale::findOrFail($data['inventory_sale_id']);
                $type = $data['settlement_type'] ?? 'full';
                $amount = $type === 'full' ? (float) $sale->debt_amount : (float) $data['amount'];
                $notes = $data['notes'] ?? null;

                $sale->settlePayment($amount, $notes);
            })
            ->successNotification(function (array $data): Notification {
                $sale = InventorySale::find($data['inventory_sale_id']);
                $debtor = $sale?->debtor_name ? " ({$sale->debtor_name})" : '';

                if ($sale && $sale->is_debt_settled) {
                    return Notification::make()
                        ->success()
                        ->title("Долг полностью погашен{$debtor}")
                        ->body('Задолженность закрыта.');
                }

                $remaining = $sale ? $sale->debt_amount : 0;

                return Notification::make()
                    ->success()
                    ->title("Платеж внесен{$debtor}")
                    ->body("Остаток долга: {$remaining} UAH.");
            });
    }
}
