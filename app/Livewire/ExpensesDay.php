<?php

namespace App\Livewire;

use App\Models\Expense;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;

class ExpensesDay extends BaseWidget
{

    protected static ?string $pollingInterval = null;

    protected $listeners = ['updateTodayShift' => '$refresh'];

    public static function badge(): ?string
    {
        return Expense::query()->with('supplies')->whereDate('added_at', '>=', Carbon::today())->count();
    }

    protected function getTableHeading(): string | Htmlable
    {
        return __('Expenses of Shift');
    }

    public function table(Table $table): Table
    {
        return $table
        ->deferLoading()
        ->query(Expense::query()->where('place_id', Filament::getTenant()->id)
        ->whereDate('added_at', Carbon::today())->with('user', 'supply')->wherePay('1'))

            ->columns([
                TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
                    ->size('sm')
                    ->description(fn (Expense $record): string => $record->user->name ?? ''),
                TextColumn::make('category.name')->label(__('Category'))->badge()
                ->description(fn (Expense $record): string => $record->supply->supplier->company ?? ''),
                TextColumn::make('sum')->label(__('Sum'))->money('UAH')->summarize(Sum::make()->money('UAH', 100)->label(__('Total'))),
                IconColumn::make('bill_id')->label(__('Bill'))
                ->icon(fn (string $state): string => match ($state) {
                    '0' => 'heroicon-o-banknotes',
                    '1' => 'heroicon-o-credit-card',
                })
                ->color(fn (string $state): string => match ($state) {
                    '1' => 'warning',
                    '0' => 'success',
                    default => 'gray',
                }),
                SpatieMediaLibraryImageColumn::make('supply')->label(__('Invoice'))
                ->collection('supplies')
                ->simpleLightbox()
                ->circular()
                ->stacked()
                ->limit(3)
                ->height(40)
                ->toggleable()
                ->defaultImageUrl(url('/assets/imgs/app/no-photo.png'))
                ->placeholder('No description.')
                ->visibleFrom('sm'),

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
            ]);
    }
}
