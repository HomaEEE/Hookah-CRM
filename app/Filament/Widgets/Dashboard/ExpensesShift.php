<?php

namespace App\Filament\Widgets\Dashboard;

use App\Enums\Bill as BillEnum;
use App\Models\Expense;
use Auth;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class ExpensesShift extends BaseWidget
{

    use HasWidgetShield;

    protected int | string | array $columnSpan = 'full';


    protected function getTableHeading(): string | Htmlable
    {
        return __('Expenses of Shift');
    }

    protected function getTableQuery(): Builder
    {
    return Expense::query()->where('place_id', Filament::getTenant()->id)
        ->whereDate('added_at', Carbon::today())->with('user', 'supply')->wherePay('1');
    }

    public function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 10;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableColumns(): array
    {
        return [
                TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
                    ->size('sm')
                    ->description(fn (Expense $record): string => $record->user->name ?? ''),
                TextColumn::make('category.name')->label(__('Category'))->badge()
                ->description(fn (Expense $record): string => $record->supply->supplier->company ?? ''),
                TextColumn::make('sum')->label(__('Sum'))->money('uah', true)->summarize(Sum::make()->label(__('Total')))  ,
                TextColumn::make('bill.name')->translateLabel()->badge(),
                SpatieMediaLibraryImageColumn::make('invoice')->label(__('Invoice'))
                    ->collection('supplies')
                    ->simpleLightbox()
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->height(30)
                    ->toggleable()
                    ->defaultImageUrl(url('/assets/imgs/app/no-photo.png'))
                    ->placeholder('No description.'),
                IconColumn::make('pay')
                    ->translateLabel()
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => ! $record->$name,
                        ]);
                    }),
        ];
    }

}
