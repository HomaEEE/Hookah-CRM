<?php

namespace Modules\Finance\Filament\Admin\Resources\Withdrawals\Schemas;

use Carbon\Carbon;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Finance\Models\Withdrawal;

class WithdrawalTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Withdrawal::query())
            ->columns([
                TextColumn::make('created_at')->dateTime('d/m/Y')->translateLabel()->badge(),
                TextColumn::make('sum')->translateLabel()
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total'))),
                TextColumn::make('bill.name')->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')->translateLabel()->native(false)
                            ->placeholder(fn ($state): string => now()->subMonth()->format('d/m/Y')),
                        DatePicker::make('created_until')->translateLabel()->native(false)
                            ->placeholder(fn ($state): string => now()->format('d/m/Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Расходы с '.Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Расходы до '.Carbon::parse($data['created_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
                SelectFilter::make('bill_id')
                    ->options(BillType::get()->pluck('name', 'id')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()->slideOver(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
