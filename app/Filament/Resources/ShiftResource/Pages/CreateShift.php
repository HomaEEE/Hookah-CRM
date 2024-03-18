<?php

namespace App\Filament\Resources\ShiftResource\Pages;

use App\Filament\Resources\{ExpenseResource, ShiftResource};
use App\Http\Controllers\{LocalApiController,PosterApiController};
use App\Models\{Bill,Expense, ExpenseCategory, Place,Shift, User, Wage, Withdrawal, Workshop};
use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\{CheckboxList, DateTimePicker, Hidden, Placeholder, Repeater, Select, Textarea, TextInput, ToggleButtons};
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Fieldset;

use Filament\Forms\{Get, Set};
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use GuzzleHttp\Exception\RequestException;

class CreateShift extends CreateRecord
{

    use HasWizard;

    protected static string $resource = ShiftResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $shift = $this->record;

        $wage = Place::where('id', $shift->place_id)->select('settings->wage_expense as expense')->first();

        Expense::create(
            [
                'user_id' => Auth::user()->id,
                'place_id' => $shift->place_id,
                'category_id' => $wage->expense,
                'sum' => $shift->wages,
                'bill_id' => '0',
                'comment' => 'Зарплаты',
            ]
        );

        Bill::updateOrCreate(
            ['id' => 0],
            ['balance' => (int) round( ( $shift->shift*0.987 ), 0) ]
        );

        Bill::updateOrCreate(
            ['id' => 1],
        )->increment('balance', $shift->terminal );

        Notification::make()
            ->title(__('Balances are updated'))
            ->send();

        Notification::make()
            ->title(__('Wages added in expenses'))
            ->send();

        (new PosterApiController())->closeShift($shift);

        Notification::make()
            ->title(__('Shift are closed'))
            ->send();


    }

    protected function getSteps(): array
    {
        return [

            Step::make('Poster')->translateLabel()
                ->schema(
                    [
                        Hidden::make('user_id')->default(Auth::user()->id),
                        Section::make()->schema([

                            Hidden::make('place_id')->default(Filament::getTenant()->id),

                            Actions::make([
                                Action::make('load_poster')->translateLabel()
                                    ->icon('heroicon-s-cloud-arrow-down')
                                    ->color('success')
                                    ->action(function (Get $get, Set $set) {
                                        try {
                                            $place = Place::where('id', $get('place_id'))->first();
                                            //  $posterShift =  (new PosterApi())->getCurrentShift($poster_api->id);  API
                                            $posterShift = Shift::where('place_id', $place->id)->select('shift')->latest()->first();
                                            $posterTodayShift = (new PosterApiController())->getTodayShift($place->id);

                                            $set('old_shift', $posterShift != null ? $posterShift->shift : 0);

                                            $set('total', $posterTodayShift['payed_sum_sum'] / 100);

                                            $set('cash', $posterTodayShift['payed_sum_sum'] / 100 - $posterTodayShift['payed_card_sum'] / 100);

                                            $set('terminal', $posterTodayShift['payed_card_sum'] / 100);

                                            $posterLocalExpense = (new LocalApiController())->getLocalExpenses($place->id);

                                            $set('expense', (int) $posterLocalExpense);

                                            $LocalWithdrawal = (new LocalApiController())->getLocalWithdrawal($place->id);
                                            $set('withdrawal', $LocalWithdrawal);

                                            $localTips = (new LocalApiController())->getLocalTips($place->id);
                                            $set('tips', $localTips);

                                            $posterTodayWorkshops = (new PosterApiController())->getTodayWorkshops($place->id);

                                            foreach ($posterTodayWorkshops as $ws) {
                                                if ($ws['workshop_name'] == 'Кальян') {
                                                    if ($ws['count'] != 0) {
                                                        $set('hookah', $ws['count'] / 1);
                                                    } else {
                                                        $set('hookah', 0);
                                                    }
                                                }

                                                if ($ws['workshop_name'] == 'Колбы') {
                                                    if ($ws['count'] != 0) {
                                                        $set('bulbs', $ws['count'] / 1);
                                                    } else {
                                                        $set('bulbs', 0);
                                                    }
                                                }

                                                if ($ws['workshop_name'] == 'Колбы') {
                                                    if ($ws['count'] != 0) {
                                                        $set('bulbs_price', $ws['revenue'] / 1);
                                                    } else {
                                                        $set('bulbs_price', 0);
                                                    }
                                                }

                                                if ($ws['workshop_name'] == 'Кухня') {
                                                    if ($ws['revenue'] != 0) {
                                                        $set('kitchen', $ws['revenue'] / 1);
                                                    } else {
                                                        $set('kitchen', 0);
                                                    }
                                                }

                                                if ($ws['workshop_name'] == 'Бар') {
                                                    if ($ws['revenue'] != 0) {
                                                        $set('bar', $ws['revenue'] / 1);
                                                    } else {
                                                        $set('bar', 0);
                                                    }
                                                }
                                            }


                                            Notification::make()
                                                ->title(__('Loaded data from Poster'))
                                                ->icon('heroicon-o-building-storefront')
                                                ->iconColor('success')
                                                ->send();
                                        } catch (RequestException $e) {
                                            Notification::make()
                                                ->title(__('Please, select place'))
                                                ->icon('heroicon-o-building-storefront')
                                                ->iconColor('danger')
                                                ->send();
                                            return;
                                        }
                                    })
                            ])->fullWidth()
                                ->hidden(fn (Get $get): bool => !$get('place_id')),
                        ]),

                        Fieldset::make(__('Poster'))->schema([
                            TextInput::make('total')->label(__('Total Poster'))
                                ->reactive()
                                ->numeric()
                                ->disabled()
                                ->default(0)

                                ->afterStateUpdated(
                                    function (Set $set, Get $get) {
                                        $set('cash', $get('total') - $get('terminal'));
                                    }
                                ),

                            TextInput::make('cash')->label(__('Cash Poster'))
                                ->numeric()
                                ->default(0)
                                ->reactive(),

                            TextInput::make('terminal')->label(__('Terminal Poster'))
                                ->default(0)
                                ->numeric()
                                ->reactive(),

                            TextInput::make('old_shift')->label(__('Shift Poster'))
                                ->numeric()
                                ->default(0)

                        ])->hidden(fn (Get $get): bool => !$get('total')),


                        Fieldset::make(__('Workshops'))
                            ->schema([
                                TextInput::make('bulbs')->label(__('Bulbs'))
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->numeric()
                                    ->hidden(fn (Get $get) => $get('bulbs') == 0),
                                TextInput::make('hookah')->label(__('Hookahs'))
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->numeric()
                                    ->default(0),
                                TextInput::make('bar')->label(__('Bar'))
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->numeric()
                                    ->default(0)
                                    ->hidden(fn (Get $get) => $get('bar') == 0),
                                TextInput::make('kitchen')->label(__('Kitchen'))
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->numeric()
                                    ->default(0)
                                    ->hidden(fn (Get $get) => $get('kitchen') == 0),
                            ])->columns(3)
                            ->hidden(fn (Get $get): bool => !$get('total'))
                    ]
                ),
            Step::make('Wages')->translateLabel()
                ->schema(
                     [
                        Repeater::make('users')
                            ->translateLabel()
                            ->relationship()
                            ->schema([
                                Select::make('user_id')->translateLabel()
                                    ->options(
                                        User::whereHas('place', function ($query) {
                                            $query->where('place_id', Filament::getTenant()->id);
                                        })->get()
                                            ->pluck('name', 'id')
                                    )
                                    ->required()
                                    ->reactive()
                                    ->searchable(),

                                Select::make('wage_id')->translateLabel()
                                    ->options(
                                        Wage::where('place_id', Filament::getTenant()->id)
                                            ->get()
                                            ->pluck('name', 'id')
                                    )
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->searchable()
                                    ->afterStateUpdated(
                                        function (Set $set, Get $get) {

                                            $wage = Wage::where('id', $get('wage_id'))->first();
                                            $all_procents = 0;
                                            if (isset($wage)) {
                                                foreach ($wage->procent as $num) {
                                                    $workshop = Workshop::where('workshop_id', $num['workshop_id'])->first();

                                                    if ($workshop->name == 'Бар') {
                                                        $all_procents += $get('../../bar') * $num['num'] / 100;
                                                    }
                                                    if ($workshop->name == 'Кухня') {
                                                        $all_procents += $get('../../kitchen') * $num['num'] / 100;
                                                    }
                                                    if ($workshop->name == 'Кальян') {
                                                        $all_procents += $get('../../hookah') * $num['num'] / 1;
                                                    }
                                                    if ($workshop->name == 'Колбы') {
                                                        $all_procents += $get('../../bulbs') * $num['num'] / 1;
                                                    }
                                                }

                                                if ($get('wage_id') != null) {
                                                    $set('wage', $wage->wage * $get('days'));
                                                } else {
                                                    $set('wage', 0);
                                                    $set('days', 0);
                                                }
                                            } else {
                                                $all_procents = 0;
                                                $set('days', 0);
                                            }

                                            $set('procent_day', round($all_procents, 0));
                                            $set('cashout', $get('procent_day') + $get('wage'));
                                        }
                                    ),

                                Select::make('days')->translateLabel()
                                    ->options(
                                        [
                                            '0' => '0',
                                            '0.5' => '0.5',
                                            '1' => '1',
                                            '2' => '2',
                                            '3' => '3',
                                            '4' => '4',
                                            '5' => '5',
                                            '6' => '6',
                                            '7' => '7',
                                        ]
                                    )->default(0)
                                    ->disabled(fn (Get $get) => $get('wage_id') == null)
                                    ->rules(['required'])
                                    ->afterStateUpdated(
                                        function (Set $set, Get $get, $state) {
                                            $wage = Wage::where('id', $get('wage_id'))->first();
                                            if ($get('days') != 0 || $get('wage_id') != null) {
                                                $set('wage', $state * $wage->wage);
                                                $set('cashout', $get('procent_day') + $get('wage'));
                                            }
                                        }
                                    )
                                    ->reactive(),
                                TextInput::make('wage')->translateLabel()->numeric()
                                    ->hidden(fn (Get $get) => $get('wage_id') == null)
                                    ->afterStateUpdated(
                                        function (Set $set, Get $get) {
                                            if ($get('wage') != null) {
                                                $set('cashout', $get('procent_day') + $get('wage'));
                                            } else {
                                                $set('cashout', 0);
                                            }
                                        }
                                    )->columns(2)
                                    ->live(onBlur: true),
                                TextInput::make('procent_day')->translateLabel()->numeric()
                                    ->hidden(fn (Get $get) => $get('wage_id') == null)
                                    ->afterStateUpdated(
                                        function (Set $set, Get $get) {
                                            if ($get('procent_day') != null) {
                                                $set('cashout', $get('procent_day') + $get('wage'));
                                            }
                                        }
                                    )
                                    ->reactive(),
                                TextInput::make('cashout')->translateLabel()
                                    ->live(onBlur: true)
                                    ->hidden(fn (Get $get) => $get('wage_id') == null),
                            ])->columns(3)
                            ->itemLabel(fn (array $state): ?string => $state['user.name'] ?? null),

                        Placeholder::make('wages')->translateLabel()
                            ->content(function (Set $set, $get) {
                                $totalWage = collect($get('users'))
                                    ->pluck('cashout')
                                    ->sum();
                                $set('wages', $totalWage);

                                return $totalWage;
                            })
                            ->reactive(),
                    ]
                ),
            Step::make('Shift')->translateLabel()
                ->schema(
                    [
                        Actions::make([
                            Action::make('Add Withdrawal')->label(__('Add Withdrawal'))
                                ->icon('heroicon-s-banknotes')
                                ->color('success')
                                ->modalHeading(__('Add Withdrawal'))
                                ->requiresConfirmation()
                                ->form([
                                    Hidden::make('user_id')->default(Auth::user()->id),
                                    Hidden::make('place_id')->default(Filament::getTenant()->id),
                                    TextInput::make('sum')->label(__('Sum'))->numeric()->prefix('₴')->default(3000),
                                    ToggleButtons::make('bill_id')->label(__('Bill'))
                                    ->options(Bill::get()->pluck('name', 'id'))
                                    ->default(0)
                                    ->required()
                                    ->inline()
                                    ->grouped()
                                    ->live(),
                                ])
                                ->action(function (Set $set, array $data): void {
                                    Withdrawal::create(
                                        [
                                            'place_id' => Filament::getTenant()->id,
                                            'user_id' => Auth::user()->id,
                                            'bill_id' => $data['bill_id'],
                                            'sum' => $data['sum']
                                        ]
                                    );

                                    $LocalWithdrawal = (new LocalApiController())->getLocalWithdrawal(Filament::getTenant()->id);
                                    $set('withdrawal', $LocalWithdrawal);
                                }),


                            Action::make('add_expense')->label(__('Add Expense'))
                                ->icon('heroicon-o-document-minus')
                                ->color('info')
                                ->modalHeading(__('Add Expense'))
                                ->requiresConfirmation()
                                ->form([
                                    DateTimePicker::make('added_at')
                                        ->label(__('Date Added'))
                                        ->native(false)
                                        ->seconds(false)
                                        ->maxDate(now())->default(now()),
                                    Select::make('category_id')->label(__('Category'))
                                        ->relationship('categories', 'name')
                                        ->searchable()
                                        ->required()
                                        ->reactive()
                                        ->options(ExpenseCategory::where('place_id', Filament::getTenant()->id)->pluck('name', 'id')),
                                    TextInput::make('sum')->label(__('Sum'))
                                        ->numeric()
                                        ->prefix('₴')
                                        ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                                    ToggleButtons::make('bill_id')->label(__('Type pay'))
                                        ->options(Bill::get()->pluck('name', 'id'))
                                        ->default(0)
                                        ->required()
                                        ->inline()
                                        ->grouped()
                                        ->live(),
                                    Textarea::make('comment')->translateLabel()->rows(2),
                                ])
                                ->action(function (Set $set, array $data): void {
                                    Expense::create(
                                        [
                                            'place_id' => Filament::getTenant()->id,
                                            'user_id' => Auth::user()->id,
                                            'category_id' => $data['category_id'],
                                            'sum' => $data['sum'],
                                            'added_at' => $data['added_at'],
                                            'comment' => $data['comment'],
                                            'bill_id' => $data['bill_id'],
                                            'pay' => 1
                                        ],
                                    );

                                    Notification::make()
                                        ->title(__('Expense added'))
                                        ->send();

                                    $posterLocalExpense = (new LocalApiController())->getLocalExpenses(Filament::getTenant()->id);
                                    $set('expense', (int) $posterLocalExpense);

                                    $LocalWithdrawal = (new LocalApiController())->getLocalWithdrawal(Filament::getTenant()->id);
                                    $set('withdrawal', (int) $LocalWithdrawal);
                                }),
                        ]),

                        FieldSet::make(__('Expenses & Income'))->schema([
                            TextInput::make('income')->translateLabel()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                ->numeric()
                                ->reactive()
                                ->minValue(0)
                                ->default(0),

                            TextInput::make('expense')->translateLabel()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                ->numeric()
                                ->default(0)
                                ->hidden(fn (Get $get) => $get('expense') == 0)
                                ->suffixAction(
                                    Action::make('update_expenses')
                                        ->translateLabel()
                                        ->icon('heroicon-o-arrow-path')
                                        ->action(function (Set $set) {
                                            $posterLocalExpense = (new LocalApiController())->getLocalExpenses(Filament::getTenant()->id);
                                            $set('expense', (int) $posterLocalExpense);

                                            Notification::make()
                                                ->title(__('Expenses updated'))
                                                ->send();
                                        })
                                ),

                            TextInput::make('tips')->translateLabel()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                ->numeric()->default(0)
                                ->suffixAction(
                                    Action::make('update_tips')
                                        ->translateLabel()
                                        ->icon('heroicon-o-arrow-path')
                                        ->action(function (Set $set) {
                                            $localTips = (new LocalApiController())->getLocalTips(Filament::getTenant()->id);
                                            $set('tips', (int) $localTips);

                                            Notification::make()
                                            ->title(__('Tips updated'))
                                            ->send();

                                        })
                                ),
                            TextInput::make('withdrawal')->translateLabel()
                                ->disabled()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])

                                ->hidden(fn (Get $get) => $get('withdrawal') == 0)
                                ->numeric()->default(0),

                            Hidden::make('wages')
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

                            Placeholder::make('total_shift')->translateLabel()
                                ->content(function (Set $set, Get $get) {
                                    $totalCash =
                                        $get('old_shift') + $get('cash') - $get('wages')
                                        + $get('income') - $get('expense') - $get('withdrawal');
                                    $set('calc_shift', $totalCash);
                                    return $totalCash;
                                }),

                            Hidden::make('calc_shift')->default(0),
                            Placeholder::make('different')->translateLabel()
                                ->content(function (Get $get) {
                                    if ($get('shift') != null) {
                                        $different =
                                            $get('shift') - $get('calc_shift');
                                        return $different;
                                    } else {
                                        return 0;
                                    }
                                }),
                            TextInput::make('shift')->translateLabel()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                ->numeric()
                                ->required()
                                ->reactive()
                                ->hintAction(
                                    Action::make('copy_shift')
                                        ->translateLabel()
                                        ->icon('heroicon-o-clipboard')
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
                                ->gridDirection('row')
                        ])
                    ]
                )
        ];
    }

}
