<?php

namespace App\Filament\Components\Shift;

use App\Filament\Components\{ExpenseForm, IncomeForm, WithdrawalForm};
use Auth;

use App\Http\Controllers\{LocalApiController, PosterApiController};
use App\Models\{Expense, Income, Place, Shift, Withdrawal};
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\{Fieldset, Hidden, Section, TextInput};
use Filament\Forms\{Get, Set};
use Filament\Notifications\Notification;


use Filament\Forms\Components\{Actions, Actions\Action, CheckboxList, Placeholder};
use Filament\Support\RawJs;
use Filament\Actions\CreateAction;

class ShiftWizard
{
    public static function schema(): array
    {
        return [
            // Actions::make([
            //     CreateAction::make('Add Withdrawal')->label(__('Add Withdrawal'))
            //         ->model(Withdrawal::class)
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

            //     CreateAction::make('add_income')->label(__('Add Income'))
            //         ->model(Income::class)

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

            //     CreateAction::make('add_expense')->label(__('Add Expense'))
            //         ->icon('heroicon-o-document-minus')
            //         ->color('danger')
            //         ->modalHeading(__('Add Expense'))
            //         ->model(Expense::class)
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
            //     ]),

            FieldSet::make(__('Expenses & Income'))->schema([
                TextInput::make('income')->translateLabel()
                    ->numeric()
                    ->reactive()
                    ->default(0)
                    ->suffixAction(
                        Action::make('update_incomes')
                            ->translateLabel()
                            ->icon('heroicon-o-arrow-path')
                            ->action(function (Set $set) {
                                $posterLocalIncomes = (new LocalApiController())->getLocalIncomes(Filament::getTenant()->id);
                                $set('income', (int) $posterLocalIncomes);

                                Notification::make()
                                    ->title(__('Incomes updated'))
                                    ->send();
                            })
                    ),

                TextInput::make('expense')->translateLabel()
                    ->numeric()
                    ->default(0)
                    ->hidden(fn (Get $get) => $get('expense') == 0)
                    ->suffixAction(
                        Action::make('update_expenses')
                            ->translateLabel()
                            ->icon('heroicon-o-arrow-path')
                            ->action(function (Set $set) {
                                $posterLocalExpense = (new LocalApiController())->getLocalExpenses(Filament::getTenant()->id);
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
                                $localTips = (new LocalApiController())->getLocalTips(Filament::getTenant()->id);
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



            // Placeholder::make('coupurs_calc')->translateLabel()
            // ->content(function (Set $set, $get) {
            //     $total = collect($get('coupurs'))
            //         ->pluck('sum')
            //         ->sum();
            //     $set('shift', $total);
            //     return $total;
            // })
            // ->reactive(),


            FieldSet::make('Shift')->translateLabel()->schema([
                Hidden::make('wages')->default(0),

                Placeholder::make('total_shift')->translateLabel()
                    ->content(function (Set $set, Get $get) {
                        $totalCash =
                            (float)$get('old_shift') + $get('cash') + $get('income') -
                            (float) $get('wages') - (float) $get('expense') - (float) $get('withdrawal');

                        $set('calc', $totalCash);
                        return  (int) $totalCash;
                    }),

                Hidden::make('calc_shift')->default(0),
                Placeholder::make('different')->translateLabel()
                    ->content(function (Get $get) {
                        if ($get('shift') != null) {
                            $different = (float) $get('shift') - (float) $get('calc');
                            return $different;
                        } else {
                            return (float) $get('calc');
                        }
                    }),
                TextInput::make('shift')->translateLabel()
                    ->inputMode('decimal')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->hintAction(
                        Action::make('copy')
                            ->translateLabel()
                            ->icon('heroicon-o-check-circle')
                            ->action(function (Set $set, get $get) {
                                $set('shift', $get('calc'));
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
                    ->gridDirection('row')
            ])
        ];
    }
}
