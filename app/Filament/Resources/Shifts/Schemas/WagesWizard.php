<?php

namespace App\Filament\Resources\Shifts\Schemas;

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
                ->hiddenLabel()
                ->translateLabel()
                ->relationship()
                ->addActionLabel(__('Add'))
                ->schema([
                    Select::make('user_id')->translateLabel()
                        ->options(function () {
                            $options = [];

                            $users = User::with('wage')->orderBy('name', 'asc')
                                ->orderBy('id', 'desc')
                                ->visible(true)
                                ->get()
                                ->groupBy(fn ($user) => $user->wage?->name ?? 'Без ставки');

                            $options = $users->map(function ($group) {
                                return $group->mapWithKeys(fn ($user) => [
                                    $user->id => $user->name,
                                ])->toArray();
                            })->toArray();

                            return $options;

                            // $wages = Wage::with('users')->orderBy('wage', 'desc')->get();

                            // foreach ($wages as $wage) {
                            //     $options[$wage->name] = collect($wage->users)->mapWithKeys(function ($user) {
                            //         return [$user->id => $user->name];
                            //     })->toArray();
                            // }

                            // return $options;
                        })

                        // ->options(

                        //     User::whereHas('place', function ($query) {
                        //         $query->where('place_id', Filament::getTenant()->id);
                        //     })->visible(true)->get()
                        //         ->pluck('name', 'id')
                        // )
                        ->required()
                        ->reactive()
                        ->native(false)
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
                        ->reactive()
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
                        ->reactive()
                        ->live(onBlur: true)

                        ->afterStateUpdated(
                            function (callable $set, callable $get) {
                                self::WageCalc($set, $get);
                            }
                        )
                        ->reactive(),
                    TextInput::make('wage')->translateLabel()->numeric()
                        ->live(onBlur: true)
                        ->reactive()
                        ->readOnly()
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
                ->collapsed()

                // ->deleteAction(
                //     fn (Action $action) => $action->after(fn (Get $get, Set $set) => self::updateTotals($get, $set)),
                // )
                ->itemLabel(fn (array $state): ?string => (User::find($state['user_id'] ?? null)?->name).' '.($state['cashout'] ?? '-')
                ),
            TextEntry::make('wages_text')->label(__('Wages'))
                ->state(function (Set $set, Get $get) {
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
                ->default(
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
