<?php

namespace App\Filament\Clusters\Dev\Pages;

use App\Filament\Clusters\Dev;
use App\Models\ExpenseCategory;
use App\Models\Ingredient;
use App\Models\Wage;
use Filament\Actions\Action;
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

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Clusters\Dev\Widgets\CostCalcWidgets::make(),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            // Repeater::make('ingredients')->label(__('Ingredients'))
            //     ->columns(12)
            //     ->addActionLabel(__('Add'))
            //     ->schema([
            //         Select::make('ingredient_id')
            //            // ->hiddenLabel()
            //             ->preload()
            //             ->native(false)
            //             ->searchable()
            //             ->reactive()
            //             ->options(
            //                 Ingredient::get()->pluck('ingredient', 'id')
            //             ),

            //         TextInput::make('quantity')
            //             ->reactive()
            //             ->live()
            //             ->suffix(static function (Get $get) {

            //                 if ($get('./ingredient_id') != null) {
            //                     $ingredient = Ingredient::find($get('./ingredient_id'));
            //                     if ($ingredient && $ingredient->unit instanceof \App\Enums\IngredientUnit) {
            //                         return $ingredient->unit->getLabel();
            //                     }
            //                 }

            //                 return null;

            //             })
            //             ->default(1)
            //             ->afterStateUpdated(
            //                 function (callable $set, callable $get) {
            //                     self::updateIngredientCost($get, $set);
            //                 }
            //             ),
            //         TextInput::make('ingredient_cost')->translateLabel()
            //             ->reactive(),
            //     ])
            //     ->afterStateUpdated(function (callable $set, callable $get) {
            //         self::updateTotalIngredients($get, $set);
            //     })
            //     ->columns(3),
            TextInput::make('cost_item')->label('Food Cost')->numeric(),

            Section::make('')->schema([
                Select::make('expenses')
                    ->options(ExpenseCategory::pluck('name', 'id'))
                    ->native(false)
                    ->multiple(),
                Select::make('wage')->translateLabel()
                    ->native(false)

                    ->options(Wage::pluck('name', 'id')),
                TextInput::make('counts')->label('Блюд в месяц')->default('5000')->numeric(),
            ])->columns(3),
            Section::make('')->schema([
                TextInput::make('cost_other')->label('Косвенные затраты'),
                TextInput::make('site_name')->label('Себестоимость')->readOnly(),
                TextInput::make('x2')->label('x2')->readOnly(),
                TextInput::make('x3')->label('x3')->readOnly(),
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
        $totalWage = collect($get('ingredients'))
            ->pluck('cashout')
            ->sum();
        $set('wages', $totalWage);

        return $totalWage;
    }

    private static function updateTotalIngredients(Get $get, Set $set)
    {
        $TotalIngredients = collect($get('ingredients'))
            ->pluck('ingredient_cost')
            ->sum();
        $set('cost_item', $TotalIngredients);

        return $TotalIngredients;
    }

    private static function updateIngredientCost(Get $get, Set $set)
    {
        if ($get('quantity') > 0) {
            try {
                $ingredient = Ingredient::find($get('ingredient_id'));
                dd($ingredient);

                $ingredient_cost = $ingredient * $get('quantity');

                $set('ingredient_cost', $ingredient_cost);

            } catch (ModelNotFoundException $e) {
                dd($e);
            }
        }

        return 0;
    }
}
