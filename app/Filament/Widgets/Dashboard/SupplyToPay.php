<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Supply;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;

class SupplyToPay extends BaseWidget

{
    use HasWidgetShield;

    protected int | string | array $columnSpan = 'full';


    protected function getTableHeading(): string | Htmlable
    {
        return __('Supplies to pay');
    }

    protected function getTableQuery(): Builder
    {
        return Supply::query()->where('place_id', Filament::getTenant()->id)->with('supplier')
        ->where('pay', 0);
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
                ->description(fn (Supply $record): string => $record->user->name ?? ''),
                TextColumn::make('supplier.company')->label(__('Supplier'))->badge(),
                TextColumn::make('sum')->label(__('Sum'))->money('uah', true)->summarize(Sum::make()->label(__('Total'))),
                TextColumn::make('bill.name')->translateLabel()->badge(),

                SpatieMediaLibraryImageColumn::make('invoice')->label(__('Invoice'))
                    ->collection('supplies')
                    ->simpleLightbox()
                    ->circular()
                    ->stacked()
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
