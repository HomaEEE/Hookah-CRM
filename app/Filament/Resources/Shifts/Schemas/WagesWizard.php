<?php

namespace App\Filament\Resources\Shifts\Schemas;

use App\DTO\WageCalculationDTO;
use App\Models\User;
use App\Models\Wage;
use App\Services\WageCalculator;
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
                        ->options(
                            User::orderBy('id', 'asc')
                                ->visible(true)
                                ->get()
                                ->pluck('name', 'id')
                        )
                        ->required()
                        ->preload()
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
                        ->options(array_combine([0, 0.5, 1, 2, 3, 4, 5, 6, 7], [0, 0.5, 1, 2, 3, 4, 5, 6, 7])
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

    private static function WageCalc(Set $set, Get $get): void
    {
        $userId = $get('user_id');
        if (! $userId) {
            $set('wage', 0);
            $set('procent_day', 0);
            $set('cashout', 0);

            return;
        }

        $user = User::with('wage')->find($userId);
        if (! $user || ! $user->wage) {
            return;
        }

        $days = (float) ($get('days') ?? $user->shift_day);

        $set('wage_id', $user->wage_id);
        $set('wage', $user->wage->wage);
        $set('days', $days);

        $dto = new WageCalculationDTO(
            baseWage: (float) $user->wage->wage,
            days: $days,
            manualProcent: (float) $get('procent_day'),
            wageProcentRules: $user->wage->procent,
            formValues: $get('../../') ?? []
        );

        $result = app(WageCalculator::class)->calculate($dto);

        $set('procent_day', $result['procent_day']);
        $set('cashout', $result['cashout']);
    }

    private static function updateTotals(Get $get, Set $set): void
    {
        $users = $get('users') ?? [];

        $total = collect($users)
            ->pluck('cashout')
            ->map(fn ($value) => (float) $value)
            ->sum();

        $set('wages', round($total, 2));
    }
}
