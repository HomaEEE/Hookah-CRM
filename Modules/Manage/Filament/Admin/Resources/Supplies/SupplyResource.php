<?php

namespace Modules\Manage\Filament\Admin\Resources\Supplies;

use App\Enums\NavigationGroup;
use App\Filament\Components\Forms\SupplyForm;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Manage\Filament\Admin\Resources\Supplies\Pages\ManageSupplies;
use UnitEnum;

class SupplyResource extends Resource
{
    protected static ?string $model = Supply::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Manage;

    public static function getPluralModelLabel(): string
    {
        return __('Supplies');
    }

    public static function getNavigationLabel(): string
    {
        return __('Supplies');
    }

    protected function getTableQuery(): Builder
    {
        return Supply::query()->with(['supplier', 'user', 'bill'])->fastPaginate();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                SupplyForm::configure($schema)
            );
    }

    public static function table(Table $table): Table
    {
        return SupplyTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SupplyInfolist::configure($schema);

    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSupplies::route('/'),
        ];
    }
}

// ->extraItemActions([
//     Action::make('openProduct')
//         ->tooltip('Open product')
//         ->icon('heroicon-m-arrow-top-right-on-square')
//         ->url(function (array $arguments, Repeater $component): ?string {
//             $itemData = $component->getRawItemState($arguments['item']);

//             $ingredient = Ingredient::find($itemData['ingredient_id']);

//             if (! $ingredient) {
//                 return null;
//             }

//             return IngredientResource::getUrl('index', ['record' => $ingredient]);
//         }, shouldOpenInNewTab: true)
//         ->hidden(fn (array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['ingredient_id'])),
// ])
// ->orderColumn('ingredient')
