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
use Modules\Finance\Filament\Admin\Resources\Expenses\Schemas\ExpenseForm;
use Modules\Finance\Filament\Admin\Resources\Incomes\Schemas\IncomeForm;
use Modules\Finance\Filament\Admin\Resources\Withdrawals\Schemas\WithdrawalForm;
use Modules\Finance\Models\Expense;
use Modules\Finance\Models\Income;
use Modules\Finance\Models\Withdrawal;
use Modules\Manage\Filament\Admin\Resources\Supplies\Schemas\SupplyForm;
use Modules\Manage\Models\Supply;

class ShiftWizard
{
    public static function schema(): array
    {
        return [

            Section::make()
                ->columns(5)
                ->schema([

                    Action::make('expenses_list')
                        ->label(__('Expenses list'))
                        ->icon('heroicon-o-queue-list')
                        ->extraAttributes(['style' => 'width: 100%'])
                        ->modalHeading(__('Expenses'))
                        ->modalWidth('3xl')
                        ->modalContent(function () {
                            return view('filament.tables.expenses-day');
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

                    Action::make('add_expense')->label(__('Expense'))
                        ->model(Expense::class)
                        ->color('danger')
                        ->icon('heroicon-o-document-minus')
                        ->extraAttributes(['style' => 'width: 100%'])

                        ->modalHeading(__('Add Expense'))
                        ->modalWidth('md')
                        ->slideOver()
                        ->modalIcon('heroicon-o-document-minus')
                        ->keyBindings(['command+s', 'ctrl+s'])
                        ->form(
                            ExpenseForm::configure(...)
                        )
                        ->action(function (array $data, Set $set) {
                            Expense::create($data);

                            $posterLocalExpense = (new LocalApiController)->getLocalExpenses(Filament::getTenant()->id);
                            $set('expense', (int) $posterLocalExpense);
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('Expense added'))
                                ->icon('heroicon-o-document-minus')
                        ),

                    Action::make('addIncome')->label(__('Income'))
                        ->model(Income::class)
                        ->color('info')
                        ->icon('heroicon-o-plus')
                        ->extraAttributes(['style' => 'width: 100%'])

                        ->modalHeading(__('Add Income'))
                        ->modalWidth('xs')
                        ->keyBindings(['command+s', 'ctrl+s'])
                        ->modalIcon('heroicon-o-inbox-arrow-down')
                        ->form(
                            IncomeForm::configure(...)
                        )
                        ->action(function (array $data, Set $set) {
                            Income::create($data);

                            $posterLocalIncomes = (new LocalApiController)->getLocalIncomes(Filament::getTenant()->id);
                            $set('income', (int) $posterLocalIncomes);
                        })

                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-plus-circle')
                                ->title(__('Income added'))
                        ),

                    Action::make('addWithdrawal')->label(__('Withdrawal'))
                        ->model(Withdrawal::class)
                        ->color('warning')
                        ->extraAttributes(['style' => 'width: 100%'])

                        ->icon('heroicon-o-minus')
                        ->modalHeading(__('Add Withdrawal'))
                        ->modalWidth('sm')
                        ->keyBindings(['command+s', 'ctrl+s'])
                        ->modalIcon('heroicon-o-inbox-arrow-down')
                        ->form(
                            WithdrawalForm::configure(...)
                        )
                        ->action(
                            function (array $data, Set $set) {

                                Withdrawal::create($data);

                                $LocalWithdrawal = (new LocalApiController)->getLocalWithdrawal(Filament::getTenant()->id);
                                $set('withdrawal', $LocalWithdrawal);
                                Notification::make()
                                    ->success()
                                    ->icon('heroicon-o-minus')
                                    ->title(__('Withdrawal added'));
                            }),
                ]),

            Fieldset::make(__('Expenses & Income'))->schema([
                TextInput::make('income')->translateLabel()
                    ->numeric()
                    ->readOnly()
                    ->live()
                    ->default(0)
                    ->suffixAction(
                        Action::make('update_incomes')
                            ->translateLabel()
                            ->icon('heroicon-o-arrow-path')
                            ->action(function (Set $set) {
                                $posterLocalIncomes = (new LocalApiController)->getLocalIncomes(Filament::getTenant()->id);
                                $set('income', (int) $posterLocalIncomes);

                                Notification::make()
                                    ->title(__('Incomes updated'))
                                    ->send();
                            })
                    ),

                TextInput::make('expense')->translateLabel()
                    ->numeric()
                    ->readOnly()
                    ->default(0)
                    ->suffixAction(
                        Action::make('update_expenses')
                            ->translateLabel()
                            ->icon('heroicon-o-arrow-path')
                            ->action(function (Set $set) {
                                $posterLocalExpense = (new LocalApiController)->getLocalExpenses(Filament::getTenant()->id);
                                $set('expense', (int) $posterLocalExpense);

                                Notification::make()
                                    ->title(__('Expenses updated'))
                                    ->send();
                            })
                    ),

                TextInput::make('tips')->translateLabel()
                    ->numeric()->default(0)
                    ->suffixAction(
                        Action::make('update_tips')
                            ->translateLabel()
                            ->icon('heroicon-o-arrow-path')
                            ->action(function (Set $set) {
                                $localTips = (new LocalApiController)->getLocalTips(Filament::getTenant()->id);
                                $set('tips', (int) $localTips / 100);

                                Notification::make()
                                    ->title(__('Tips updated'))
                                    ->send();
                            })
                    ),
                TextInput::make('withdrawal')->translateLabel()
                    ->numeric()
                    ->readOnly()
                    ->default(0)
                    ->live()
                    ->suffixAction(
                        Action::make('update_withdrawal')
                            ->translateLabel()
                            ->icon('heroicon-o-arrow-path')
                            ->action(function (Set $set) {
                                $posterLocalExpense = (new LocalApiController)->getLocalExpenses(Filament::getTenant()->id);
                                $set('expense', (int) $posterLocalExpense);

                                Notification::make()
                                    ->title(__('Expenses updated'))
                                    ->send();
                            })
                    ),
            ])->columns(4),

            Fieldset::make('Shift')->translateLabel()->schema([

                TextEntry::make('total_shift')->translateLabel()
                    ->state(function (Get $get, Set $set): string { // Changed content() to state()
                        // Assuming self::CalcShift() calculates a value and returns a string
                        return self::CalcShift($get, $set);
                    }),

                TextEntry::make('different')->translateLabel()
                    ->state(function (Get $get, Set $set) { // Changed content() to state()
                        if ($get('shift') != null) {
                            $different = (float) $get('shift') - (float) $get('calc_shift');

                            // This line is fine if 'different' needs to be stored in the form data
                            $set('different', $different);

                            return number_format($different, 2);

                        } else {
                            // Display 'calc_shift' value if 'shift' is null
                            return $get('calc_shift');
                        }
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
                    ),

            ])->columns(['md' => 2, 'xl' => 3]),

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

    private static function CalcShift(Get $get, Set $set)
    {
        $totalCash =
            (float) $get('old_shift') + (float) $get('cash') + (float) $get('income') -
            (float) $get('wages') - (float) $get('expense') - (float) $get('withdrawal');

        $set('calc_shift', $totalCash);

        return $totalCash;
    }
}
