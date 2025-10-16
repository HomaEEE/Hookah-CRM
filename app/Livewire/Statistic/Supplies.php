<?php

namespace App\Livewire\Statistic;

use App\Models\Supplier;
use App\Models\Supply;
use Filament\Facades\Filament;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Malzariey\FilamentDaterangepickerFilter\Enums\OpenDirection;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class Supplies extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    use InteractsWithPageFilters;

    protected function getTableHeading(): string
    {
        return __('Supplies');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Supply::query()->where('place_id', Filament::getTenant()->id)
            )
            ->columns([
                TextColumn::make('supplier.company')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('count')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('total')
                    ->label(__('Sum'))
                    ->sortable()
                    ->money('UAH', 100)
                    ->summarize(Sum::make()->money('UAH', 100)->label(__('Total'))),
            ])

            ->filters([
                SelectFilter::make('supplier_id')
                    ->label('Категория')
                    ->native(false)
                    ->options(
                        Supplier::query()
                            ->pluck('company', 'id')
                            ->toArray()
                    ),
                DateRangeFilter::make('created_at')
                    ->translateLabel()
                    ->defaultThisMonth()
                    ->showDropdowns()->opens(OpenDirection::LEFT),

            ])
            ->hiddenFilterIndicators()
            ->modifyQueryUsing(function ($query) {
                // Use HasFiltersForm trait for date filters
                $startDate = $this->filters['from'] ?? null;
                $endDate = $this->filters['to'] ?? null;

                // Применение фильтрации по датам, если значения присутствуют
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }

                $query->where('pay', 1)->selectRaw('supplier_id as id, supplier_id, sum(sum) as total, count(*) as count')
                    ->groupBy('supplier_id');
            });
    }
}
