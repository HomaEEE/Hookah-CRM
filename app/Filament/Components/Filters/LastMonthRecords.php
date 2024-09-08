<?php

namespace App\Filament\Components\Filters;

use Auth;
use Carbon\Carbon;
use App\Models\{Expense, Supplier, Bill, ExpenseCategory};
use Filament\Facades\Filament;

use Filament\Forms\{Get, Set};
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\DB;
class LastMonthRecords
{
    public static function schema(): array
    {
        return [
            Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->translateLabel()->native(false)
                            ->default(now()->subMonth())
                            ->placeholder(fn ($state): string => now()->subMonth()->format('d/m/Y')),
                        DatePicker::make('created_until')->translateLabel()->native(false)
                            ->default(now())
                            ->placeholder(fn ($state): string => now()->format('d/m/Y')),
                    ])
                    ->default()
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
                            $indicators['created_from'] = 'С ' . Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'До ' . Carbon::parse($data['created_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
                ];
            }
        }