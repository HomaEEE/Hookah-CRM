<?php

namespace Modules\Finance\Livewire;

use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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
use Modules\Finance\Enums\TransactionType;
use Modules\Finance\Models\Bill;
use Modules\Finance\Models\Transaction;

class ExpensesDay extends BaseWidget implements HasTable
{
    // protected static ?string $pollingInterval = null;

    // protected $listeners = ['refreshExpensesList' => 'loadExpenses'];

    protected $listeners = ['refreshExpensesDay' => '$refresh'];

    use InteractsWithTable;

    public static function badge(): ?string
    {
        return Transaction::query()->with('supplies')->whereDate('added_at', '=', Carbon::today())->count();
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
            ->query(Transaction::query()->where('type', TransactionType::Expense->value)
                ->whereDate('added_at', Carbon::today())->with('user', 'supply'))

            ->columns([
                TextColumn::make('category.name')->label(__('Category'))->badge()
                    ->description(fn (Transaction $record): string => $record->supply->supplier->company ?? $record->comment ?? ''),
                TextColumn::make('sum')->label(__('Sum'))->money('UAH')->summarize(Sum::make()->money('UAH', 100)->label(__('Total'))),
                IconColumn::make('bill.type')->label(__('Bill'))
                    ->action(function ($record) {
                        $record->update([
                            'bill_id' => ! $record->bill_id,
                        ]);
                    }),
                SpatieMediaLibraryImageColumn::make('supply.media')->label(__('Invoice'))
                    ->state(function (Transaction $record) {
                        return $record->supply_id ? $record->supply->getFirstMediaUrl('images') : 'invoice'; // Или путь к заглушке
                    })
                    ->collection('supplies')
                    ->lightbox()
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->height(40)
                    ->toggleable()
                    ->defaultImageUrl(url('/assets/imgs/app/no-photo.png'))
                    ->visibleFrom('sm'),

            ])
            ->recordActions([
                EditAction::make()->label('')
                    ->modalWidth('sm')
                    ->schema([
                        Tabs::make('Tabs')
                            ->contained(false)
                            ->tabs([
                                Tab::make(__('Expense'))
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

                                    ]),
                                Tab::make(__('Photo'))
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('invoice')
                                            ->label(__('Photo'))
                                            ->collection('supplies')
                                            ->multiple()
                                            ->imageResizeMode('cover')
                                            ->imagePreviewHeight(250)
                                            ->customProperties(['format' => 'webp', 'width' => 1800, 'quality' => 50])
                                            ->openable(),
                                        Textarea::make('comment')->label(__('Comment'))
                                            ->rows(3),
                                    ]),

                            ]),
                    ]),

                DeleteAction::make(),
            ]);
    }
}
