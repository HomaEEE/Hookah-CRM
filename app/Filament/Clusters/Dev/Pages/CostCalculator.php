<?php

namespace App\Filament\Clusters\Dev\Pages;

use App\Filament\Clusters\Dev;
use App\Models\Ingredient;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CostCalculator extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static string $view = 'filament.clusters.modules.pages.cost-calculator';

    protected static ?string $cluster = Dev::class;

    public static function getPluralModelLabel(): string
    {
        return __('Cost Calculator');
    }

    public static function getNavigationLabel(): string
    {
        return __('Cost Calculator');
    }

    public function getHeading(): string
    {
        return __('Cost Calculator');
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('')->schema([
                Repeater::make('ingredients')
                    ->schema([
                        Select::make('ingredient_id')
                            ->hiddenLabel()
                            ->preload()
                            ->native(false)
                            ->searchable()
                            ->columnSpan(4)
                            ->reactive()
                            ->live()
                            ->options(
                                Ingredient::where('place_id', Filament::getTenant()->id)->pluck('ingredient', 'id')
                            ),
                        TextInput::make('quantity')
                            ->reactive()
                            ->live()
                            ->columnSpan(4)
                            // ->hiddenLabel()
                            ->suffix(static function (Get $get) {

                                if ($get('ingredient_id') != null) {
                                    $ingredient = Ingredient::find($get('ingredient_id'));
                                    if ($ingredient && $ingredient->unit instanceof \App\Enums\IngredientUnit) {
                                        return $ingredient->unit->getLabel();
                                    }
                                }

                                return null;

                            })
                            ->default(1)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                if (! $get('quantity')) {
                                    self::updateIngredientCost($get, $set);
                                }
                            }),
                        TextInput::make('ingredient_cost')->translateLabel()
                            ->reactive()
                            ->columnSpan(4),
                    ])->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotalIngredients($get, $set);
                    }),
                TextInput::make('cost_item')->label('Прямые затраты')->readOnly(),
            ]),

            Section::make('')->schema([
                TextInput::make('rent')->translateLabel(),
                TextInput::make('wage')->translateLabel(),
                TextInput::make('utils')->translateLabel(),  // Прочие расходы
            ])->columns(3),
            Section::make('')->schema([
                TextInput::make('cost_other')->label('Косвенные затраты'),
                TextInput::make('site_name')->label('Себестоимость'),
                TextInput::make('x2')->label('x2'),
                TextInput::make('x3')->label('x3'),
            ])->columns(4),

        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send-to-bot')->label('')->icon('heroicon-o-paper-airplane'),
        ];
    }

    public function getLastMonthExpenses($place_id) {}

    private static function updateTotalsIngredients(Get $get, Set $set)
    {
        $totalWage = collect($get('users'))
            ->pluck('cashout')
            ->sum();
        $set('wages', $totalWage);

        return $totalWage;
    }

    private static function updateTotalIngredients(Get $get, Set $set)
    {
        $TotalIngredients = collect($get('ingredients'))
            ->pluck('ingredient_costcashout')
            ->sum();
        $set('cost_item', $TotalIngredients);

        return $TotalIngredients;
    }

    private static function updateIngredientCost(Get $get, Set $set)
    {
        if ($get('quantity') > 0) {
            try {
                $ingredient = Ingredient::find($get('ingredient_id'));
            } catch (ModelNotFoundException $e) {
                dd($e);
            }

            $TotalIngredients = $ingredient * $get('quantity');

            dd($TotalIngredients);

            $set('ingredient_cost', $TotalIngredients);
        }

        return 0;
    }
}
