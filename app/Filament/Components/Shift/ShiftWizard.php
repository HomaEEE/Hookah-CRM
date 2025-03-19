<?php

namespace App\Filament\Components\Shift;

use App\Filament\Components\ExpenseForm;
use App\Filament\Components\IncomeForm;
use App\Filament\Components\WithdrawalForm;
use App\Http\Controllers\LocalApiController;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Place;
use App\Models\Withdrawal;
use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;

class ShiftWizard
{
    public static function schema(): array
    {
        return [

            // Actions::make([
            //     Action::make('Add Withdrawal')->label(__('Add Withdrawal'))
            //         ->icon('heroicon-s-banknotes')
            //         ->color('success')
            //         ->modalHeading(__('Add Withdrawal'))
            //         ->requiresConfirmation()
            //         ->form(
            //             WithdrawalForm::schema()
            //         )
            //         ->action(function (Set $set, array $data): void {
            //             // Withdrawal::create(
            //             //     [
            //             //         'place_id' => Filament::getTenant()->id,
            //             //         'user_id' => Auth::user()->id,
            //             //         'bill_id' => $data['bill_id'],
            //             //         'sum' => $data['sum']
            //             //     ]
            //             // );

            //             $LocalWithdrawal = (new LocalApiController())->getLocalWithdrawal(Filament::getTenant()->id);
            //             $set('withdrawal', $LocalWithdrawal / 100);
            //         }),

            //         Action::make('add_income')->label(__('Add Income'))
            //         ->icon('heroicon-o-document-plus')
            //         ->color('info')
            //         ->modalHeading(__('Add Income'))
            //         ->requiresConfirmation()
            //         ->form(
            //             IncomeForm::schema()
            //         )
            //         ->action(function (Set $set, array $data): void {
            //             // Income::create(
            //             //     [
            //             //         'place_id' => Filament::getTenant()->id,
            //             //         'user_id' => Auth::user()->id,
            //             //         'sum' => $data['sum'],
            //             //         'comment' => $data['comment'],
            //             //     ],
            //             // );

            //             Notification::make()
            //                 ->title(__('Income added'))
            //                 ->send();

            //             $posterLocalIncomes = (new LocalApiController())->getLocalIncomes(Filament::getTenant()->id);
            //             $set('income', (int) $posterLocalIncomes / 100);
            //         }),

            //         Action::make('add_expense')->label(__('Add Expense'))
            //         ->icon('heroicon-o-document-minus')
            //         ->color('danger')
            //         ->modalHeading(__('Add Expense'))
            //         ->requiresConfirmation()
            //         ->form(
            //             ExpenseForm::schema()
            //         )
            //         ->action(function (Set $set, array $data): void {
            //             // Expense::create(
            //             //     [
            //             //         'place_id' => Filament::getTenant()->id,
            //             //         'user_id' => Auth::user()->id,
            //             //         'category_id' => $data['category_id'],
            //             //         'sum' => $data['sum'],
            //             //         'added_at' => $data['added_at'],
            //             //         'comment' => $data['comment'],
            //             //         'bill_id' => $data['bill_id'],
            //             //         'pay' => 1
            //             //     ],
            //             // );

            //             Notification::make()
            //                 ->title(__('Expense added'))
            //                 ->send();

            //             $posterLocalExpense = (new LocalApiController())->getLocalExpenses(Filament::getTenant()->id);
            //             $set('expense', (int) $posterLocalExpense / 100);
            //         }),
            //     ])->fullWidth(),

            FieldSet::make(__('Expenses & Income'))->schema([
                TextInput::make('income')->translateLabel()
                    ->numeric()
                    ->readOnly()
                    ->reactive()
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
                                $set('expense', (int) $posterLocalExpense / 100);

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
                    ->default(0)
                    ->reactive()
                    ->hidden(fn (Get $get) => $get('withdrawal') == 0),
            ])->columns(3),

            FieldSet::make('Shift')->translateLabel()->schema([

                Placeholder::make('total_shift')->translateLabel()
                    ->content(function (Get $get, Set $set): string {
                        return self::CalcShift($get, $set);
                    }),

                Placeholder::make('different')->translateLabel()
                    ->content(function (Get $get, Set $set) {
                        if ($get('shift') != null) {
                            $different = (float) $get('shift') - (float) $get('calc_shift');
                            $set('different', $different);

                            return number_format($different, 2);

                        } else {
                            return $get('calc_shift');
                        }
                    }),

                Hidden::make('calc_shift')->translateLabel(),

                TextInput::make('shift')->translateLabel()
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->live(onBlur: true)
                    ->suffixAction(
                        Action::make('aproove')
                            ->label(__('Approve'))
                            ->icon('heroicon-o-check-circle')
                            ->action(function (Set $set, get $get) {
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
