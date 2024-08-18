<?php

namespace App\Livewire;

use App\Models\Supply;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class Paying extends BaseWidget
{

    use HasWidgetShield;

    protected function getTableHeading(): string | Htmlable
    {
        return __('Supplies to pay');
    }

    public function table(Table $table): Table
    {
        return $table
        ->deferLoading()
        ->query(Supply::query()->where('place_id', Filament::getTenant()->id)->with('supplier')
        ->where('pay', 0))
            ->columns([
                TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
                ->size('sm')
                ->description(fn (Supply $record): string => $record->user->name ?? ''),
                TextColumn::make('supplier.company')->label(__('Supplier'))->badge(),
                TextColumn::make('sum')->label(__('Sum'))->money('uah')->summarize(Sum::make()->money('UAH', 100)->label(__('Total'))),

                IconColumn::make('bill_id')->label(__('Bill'))
                ->icon(fn (string $state): string => match ($state) {
                    '0' => 'heroicon-o-banknotes',
                    '1' => 'heroicon-o-credit-card',
                })
                ->color(fn (string $state): string => match ($state) {
                    '1' => 'warning',
                    '0' => 'success',
                    default => 'gray',
                })
                ->action(function ($record, $column) {
                    $name = $column->getName();
                    $record->update([
                        $name => ! $record->$name,
                    ]);
                }),

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
            ])->defaultSort('created_at', 'desc');
    }
}
