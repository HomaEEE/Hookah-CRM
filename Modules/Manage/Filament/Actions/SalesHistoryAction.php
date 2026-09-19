<?php

namespace Modules\Manage\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Modules\Manage\Models\InventoryItem;
use Modules\Manage\Models\InventorySale;

class SalesHistoryAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'salesHistory';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('История продаж')
            ->icon(Heroicon::Clock)
            ->color('gray')
            ->modalHeading(fn (InventoryItem $record): string => "История продаж: {$record->name}")
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Закрыть')
            ->modalWidth('4xl')
            ->infolist([
                RepeatableEntry::make('sales')
                    ->hiddenLabel()
                    ->placeholder('Продажи по этой позиции отсутствуют')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Дата и время')
                            ->dateTime('d.m.Y H:i'),

                        TextEntry::make('quantity')
                            ->label('Количество')
                            ->suffix(' шт.')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('price')
                            ->label('Цена за шт.')
                            ->money('UAH'),

                        TextEntry::make('totalPrice')
                            ->label('Сумма сделки')
                            ->money('UAH')
                            ->color('success')
                            ->weight('bold'),

                        TextEntry::make('debt_status')
                            ->label('Статус оплаты')
                            ->badge()
                            ->state(function (InventorySale $record): string {
                                if (! $record->is_debt) {
                                    return 'Оплачено';
                                }

                                return $record->is_debt_settled
                                    ? 'Долг погашен'
                                    : "В долг: {$record->debt_amount} UAH";
                            })
                            ->color(function (InventorySale $record): string {
                                if (! $record->is_debt || $record->is_debt_settled) {
                                    return 'success';
                                }

                                return 'danger';
                            })
                            ->suffixAction(
                                Action::make('settleDebt')
                                    ->label('Внести платеж')
                                    ->icon(Heroicon::Banknotes)
                                    ->color('warning')
                                    ->visible(fn (InventorySale $record): bool => (bool) $record->is_debt && ! (bool) $record->is_debt_settled)
                                    ->schema([
                                        TextInput::make('amount')
                                            ->label('Сумма платежа (UAH)')
                                            ->numeric()
                                            ->required()
                                            ->minValue(0.01)
                                            ->default(fn (InventorySale $record) => $record->debt_amount)
                                            ->helperText(fn (InventorySale $record) => "Остаток долга: {$record->debt_amount} UAH"),
                                        TextInput::make('note')
                                            ->label('Примечание к платежу')
                                            ->nullable(),
                                    ])
                                    ->action(function (InventorySale $record, array $data): void {
                                        $record->settlePayment((float) $data['amount'], $data['note'] ?? null);
                                        Notification::make()
                                            ->success()
                                            ->title('Платеж зафиксирован')
                                            ->body("Остаток долга: {$record->debt_amount} UAH")
                                            ->send();
                                    })
                            ),

                        TextEntry::make('debtor_name')
                            ->label('Покупатель / Должник')
                            ->placeholder('—')
                            ->badge()
                            ->color('gray')
                            ->visible(fn (InventorySale $record): bool => (bool) $record->is_debt),

                        TextEntry::make('paid_amount')
                            ->label('Оплачено')
                            ->money('UAH')
                            ->color('info')
                            ->visible(fn (InventorySale $record): bool => (bool) $record->is_debt),

                        TextEntry::make('debt_amount')
                            ->label('Остаток долга')
                            ->money('UAH')
                            ->color(fn (InventorySale $record): string => $record->debt_amount > 0 ? 'danger' : 'gray')
                            ->visible(fn (InventorySale $record): bool => (bool) $record->is_debt),

                        TextEntry::make('serial_numbers')
                            ->label('Серийные номера (SN)')
                            ->badge()
                            ->separator(',')
                            ->placeholder('—'),

                        TextEntry::make('user.name')
                            ->label('Сотрудник')
                            ->placeholder('—'),

                        TextEntry::make('notes')
                            ->label('Примечание')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}
