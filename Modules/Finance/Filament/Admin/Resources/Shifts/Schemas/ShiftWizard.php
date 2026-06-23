<?php

namespace Modules\Finance\Filament\Admin\Resources\Shifts\Schemas;

use App\Http\Controllers\LocalApiController;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Modules\Finance\app\Services\CashBalanceService;
use Modules\Finance\Filament\Admin\Resources\Transactions\Schemas\TransactionForm;
use Modules\Finance\Models\Transaction;
use Modules\Manage\Filament\Admin\Resources\Supplies\Schemas\SupplyForm;
use Modules\Manage\Models\Supply;

class ShiftWizard
{
    public static function schema(): array
    {
        return [

            Section::make()
                ->columns(3)
                ->schema([

                    Action::make('expenses_list')
                        ->label(__('Expenses list'))
                        ->icon('heroicon-o-queue-list')
                        ->extraAttributes(['style' => 'width: 100%'])
                        ->modalHeading(__('Expenses'))
                        ->modalWidth('6xl')
                        ->modalContent(function () {
                            return view('Finance::tables.expenses-day');
                        }),

                    Action::make('add_supply')->label(__('Supply'))
                        ->model(Supply::class)
                        ->icon('heroicon-o-inbox-arrow-down')
                        ->extraAttributes(['style' => 'width: 100%'])
                        ->modalHeading(__('Add Supply'))
                        ->modalWidth('md')
                        ->color('success')
                        ->keyBindings(['command+s', 'ctrl+s'])
                        ->modalIcon('heroicon-o-inbox-arrow-down')
                        ->slideOver()
                        ->form(
                            SupplyForm::configure(...)
                        )
                        ->action(function (array $data, Set $set) {
                            Supply::create($data);

                            $posterLocalExpense = (new LocalApiController)->getLocalExpenses(Filament::getTenant()->id);
                            $set('expense', (int) $posterLocalExpense);
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-inbox-arrow-down')
                                ->title(__('Supply added'))
                        ),

                    Action::make('add_transaction')->label(__('Transaction'))
                        ->model(Transaction::class)
                        ->color('warning')
                        ->icon('heroicon-o-currency-dollar')
                        ->extraAttributes(['style' => 'width: 100%'])

                        ->modalHeading(__('Add Transaction'))
                        ->modalWidth('md')
                        ->slideOver()
                        ->modalIcon('heroicon-o-document-minus')
                        ->keyBindings(['command+s', 'ctrl+s'])
                        ->form(
                            TransactionForm::configure(...)
                        )
                        ->action(function (array $data, Set $set) {
                            Transaction::create($data);

                            $posterLocalExpense = (new LocalApiController)->getLocalExpenses(Filament::getTenant()->id);
                            $set('expense', (int) $posterLocalExpense);
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('Expense added'))
                                ->icon('heroicon-o-document-minus')
                        ),
                ]),

            Fieldset::make('Shift')->translateLabel()->schema([

                Hidden::make('tips')->default(0),
                // ->suffixAction(
                //     Action::make('update_tips')
                //         ->translateLabel()
                //         ->icon('heroicon-o-arrow-path')
                //         ->action(function (Set $set) {
                //             $localTips = (new LocalApiController)->getLocalTips(Filament::getTenant()->id);
                //             $set('tips', (int) $localTips / 100);

                //             Notification::make()
                //                 ->title(__('Tips updated'))
                //                 ->send();
                //         })
                // ),

                TextEntry::make('total_shift')->translateLabel()
                    ->size('md')
                    ->color('success')
                    ->state(function (Get $get, Set $set): string {
                        $final_balance = app(CashBalanceService::class)->getCurrentBalance(
                            Filament::getTenant()->id,
                            $get('cash'),
                            $get('wages')
                        );

                        $set('calc_shift', $final_balance);

                        return (string) $final_balance;
                    }),

                Hidden::make('calc_shift')->translateLabel(),

                TextInput::make('shift')->translateLabel()
                    ->numeric()
                    ->required()
                    ->live()
                    ->live(onBlur: true)
                    ->suffixAction(
                        Action::make('aproove')
                            ->label(__('Approve'))
                            ->icon('heroicon-o-check-circle')
                            ->action(function (Set $set, Get $get) {
                                $set('shift', $get('calc_shift'));
                            })
                    )
                    ->helperText(function (Get $get) {
                        $debtGrid = $get('shift') - $get('calc_shift');

                        return "Разница: {$debtGrid} ₴";
                    }),

            ])->columns(['xs' => 2, 'md' => 2, 'xl' => 2]),

            Fieldset::make(__('Reports'))->schema([
                CheckboxList::make('report')->hiddenLabel()
                    ->bulkToggleable()
                    ->options([
                        'x-report' => __('X report'),
                        'z-report' => __('Z report'),
                    ])
                    ->descriptions([
                        'x-report' => __('X-report of the terminal and poster are reconciled'),
                        'z-report' => __('General reports taken from terminal and poster'),
                    ])
                    ->gridDirection('row'),
            ]),
        ];
    }
}
