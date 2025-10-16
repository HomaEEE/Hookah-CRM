<?php

namespace App\Filament\Components\Shift;

use App\Models\User;
use App\Models\Wage;
use App\Models\Workshop;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Size;

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
                        ->searchable()
                        ->live(onBlur: true)
                        ->afterStateUpdated(
                            function (callable $set, callable $get) {
                                self::WageCalc($set, $get);
                            }
                        ),

                    Select::make('wage_id')->translateLabel()
                        ->options(
                            Wage::where('place_id', Filament::getTenant()->id)
                                ->get()
                                ->pluck('name', 'id')
                        )
                        ->preload()
                        ->required()
                        ->live(onBlur: true)
                        ->searchable()
                        ->hidden(fn (Get $get) => $get('user_id') == null)
                        ->afterStateUpdated(
                            function (callable $set, callable $get) {
                                self::WageCalc($set, $get);
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
                        ->hidden(fn (Get $get) => $get('user_id') == null)
                        ->required()
                        ->afterStateUpdated(
                            function (callable $set, callable $get) {
                                self::WageCalc($set, $get);
                            }
                        )
                        ->reactive(),
                    TextInput::make('wage')->translateLabel()->numeric()
                        ->hidden(fn (Get $get) => $get('wage_id') == null)
                        ->columns(2),

                    TextInput::make('procent_day')->translateLabel()->numeric()
                        ->prefix('%')
                        ->default(0)
                        ->live(onBlur: true)
                        ->suffixAction(
                            Action::make('Reset')
                                ->size(Size::Large)
                                ->icon('heroicon-o-x-mark')
                                ->iconButton()
                                ->color('danger')
                                ->disabled(fn (Get $get): bool => empty($get('procent_day')) || $get('procent_day') == 0)
                                ->action(function (Get $get, Set $set, $state): void {
                                    $set('procent_day', 0);
                                    self::WageCalc($set, $get);
                                })
                        )
                        ->hidden(fn (Get $get) => $get('wage_id') == null)
                        ->afterStateUpdated(
                            function (callable $set, callable $get) {
                                self::WageCalc($set, $get);
                            }
                        )
                        ->reactive(),
                    TextInput::make('cashout')->translateLabel()
                        ->readOnly()
                        ->reactive()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::updateTotals($get, $set);
                        })
                        ->hidden(fn (Get $get) => $get('wage_id') == null),
                ])->columns(3)
                ->collapsible()
                // ->deleteAction(
                //     fn (Action $action) => $action->after(fn (Get $get, Set $set) => self::updateTotals($get, $set)),
                // )

                ->itemLabel(fn (array $state): ?string => User::find($state['user_id'])?->name.' '.$state['cashout'] ?? null),

            TextEntry::make('wages')->translateLabel()
                ->content(function (Set $set, $get) {
                    $totalWage = collect($get('users'))
                        ->pluck('cashout')
                        ->sum();
                    $set('wages', $totalWage);

                    return $totalWage;
                })
                ->reactive(),

            Hidden::make('wages')
                ->inlineLabel()
                ->reactive()
                ->translateLabel()->default(
                    function (Set $set, $get) {
                        $totalWage = collect($get('users'))
                            ->pluck('cashout')
                            ->sum();
                        $set('wages', $totalWage);

                        return $totalWage;
                    }),

        ];
    }

    private static function WageCalc(Set $set, Get $get)
    {
        if ($get('user_id') !== null) {
            $user = User::where('id', $get('user_id'))->first();
            $set('wage_id', $user->wage_id);
            $set('wage', $user->wage->wage);

            if ($get('days') == null) {
                $set('days', $user->shift_day);
            } else {
                $get('days');
            }

            if (isset($user->wage)) {

                if ($get('procent_day') == 0) {
                    $all_procent = 0;
                    foreach ($user->wage->procent as $workshop) {
                        $field = Workshop::where('workshop_id', $workshop['workshop_id'])->first()->toArray();
                        $all_procent += $get('../../'.$field['field']) * $workshop['num'] / 1;
                    }

                    $round_procent = $all_procent % 1;

                    if ($round_procent < 5 || $round_procent > 8) {
                        $roundedToTen = round($all_procent, -1);
                        $set('procent_day', $roundedToTen % 2 == 0 ? $roundedToTen : $roundedToTen + ($roundedToTen > 0 ? -10 : 10));
                    }
                } else {
                    $set('procent_day', $get('procent_day'));
                }
            }
            $set('cashout', ($get('wage') * $get('days')) + $get('procent_day'));
        } else {
            $set('wage', 0);
            $set('procent_day', 0);
            $set('cashout', 0);
        }
    }

    private static function updateTotals(Get $get, Set $set)
    {
        $totalWage = collect($get('users'))
            ->pluck('cashout')
            ->sum();
        $set('wages', $totalWage);

        return $totalWage;
    }
}
