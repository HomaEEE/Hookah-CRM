<?php

namespace App\Filament\Components\Shift;

use Auth;

use App\Models\{User, Wage, Workshop};
use Filament\Facades\Filament;
use Filament\Forms\Components\{TextInput};
use Filament\Forms\{Get, Set};

use Filament\Forms\Components\{Placeholder, Repeater, Select};
use Filament\Forms\Components\Wizard\Step;

class WagesWizard
{
    public static function schema(): array
    {
        return [
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
                                ->live(debounce: 500)
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
                                ->required()
                                ->live(debounce: 500)
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
                                ->live(debounce: 500),
                            TextInput::make('procent_day')->translateLabel()->numeric()
                                ->hidden(fn (Get $get) => $get('wage_id') == null)
                                ->afterStateUpdated(
                                    function (Set $set, Get $get) {
                                        if ($get('procent_day') != null) {
                                            $set('cashout', $get('procent_day') + $get('wage'));
                                        }
                                    }
                                )
                                ->live(debounce: 500),
                            TextInput::make('cashout')->translateLabel()
                                ->live(debounce: 500)
                                ->readOnly()
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
                        ->live(debounce: 500)
        ];
    }
}
