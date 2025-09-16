<?php

namespace App\Livewire\Dashboard;

use App\Models\Bill;
use App\Models\Expense;
use Carbon\Carbon;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Actions;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;

class ExpensesDay extends BaseWidget implements HasTable
{
    // protected static ?string $pollingInterval = null;

    // protected $listeners = ['refreshExpensesList' => 'loadExpenses'];

    use InteractsWithTable;

    public static function badge(): ?string
    {
        return Expense::query()->with('supplies')->whereDate('added_at', '>=', Carbon::today())->count();
    }

    protected function getTableHeading(): string|Htmlable
    {
        return __('Expenses of Shift');
    }

    public function table(Table $table): Table
    {

        return $table
            ->defaultGroup('bill.name')
            ->groups([
                Group::make('bill.name')->label(__('Bill'))
                    ->titlePrefixedWithLabel(false),
                Group::make('category.name')->label(__('Category'))
                    ->titlePrefixedWithLabel(false),
            ])
            ->groupingSettingsInDropdownOnDesktop()
            ->query(Expense::query()->where('place_id', Filament::getTenant()->id)
                ->whereDate('added_at', Carbon::today())->with('user', 'supply')->wherePay('1'))

            ->columns([
                TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
                    ->size('sm')
                    ->description(fn (Expense $record): string => $record->user->name ?? ''),
                TextColumn::make('category.name')->label(__('Category'))->badge()
                    ->description(fn (Expense $record): string => $record->supply->supplier->company ?? $record->comment ?? ''),
                TextColumn::make('sum')->label(__('Sum'))->money('UAH')->summarize(Sum::make()->money('UAH', 100)->label(__('Total'))),
                IconColumn::make('bill.type')->label(__('Bill'))
                    ->action(function ($record) {
                        $record->update([
                            'bill_id' => ! $record->bill_id,
                        ]);
                    }),
                SpatieMediaLibraryImageColumn::make('supply.media')->label(__('Invoice'))
                    ->state(function (Expense $record) {
                        return $record->supply_id ? $record->supply->getFirstMediaUrl('images') : 'invoice'; // Или путь к заглушке
                    })
                    ->collection('supplies')
                    ->simpleLightbox()
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->height(40)
                    ->toggleable()
                    ->defaultImageUrl(url('/assets/imgs/app/no-photo.png'))
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
            ])
            ->recordActions([
                //  Actions\ViewAction::make()->label(''),
                EditAction::make()->label('')
                    ->modalWidth('xs')

                    ->schema([
                        TextInput::make('sum')->translateLabel(),
                        ToggleButtons::make('bill_id')->label(__('Type pay'))
                            ->options(Bill::get()->pluck('name', 'id'))
                            ->default(0)
                            ->required()
                            ->inline()
                            ->grouped()
                            ->extraAttributes(['style' => 'width: 100%'])
                            ->colors([
                                0 => 'info',
                                1 => 'success',
                            ])
                            ->reactive(),

                        ToggleButtons::make('pay')->label(__('Pay'))
                            ->options(
                                [
                                    0 => __('No pay'),
                                    1 => __('Paid'),
                                ]
                            )
                            ->colors([
                                0 => 'danger',
                                1 => 'success',
                            ])
                            ->default(0)
                            ->inline()
                            ->live()
                            ->grouped()
                            ->extraAttributes(['style' => 'width: 100%'])
                            ->reactive(),
                    ]
                    ),
            ]);
    }
}
