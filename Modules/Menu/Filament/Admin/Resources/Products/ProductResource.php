<?php

namespace Modules\Menu\Filament\Admin\Resources\Products;

use App\Enums\NavigationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Menu\Filament\Admin\Resources\Products\Pages\ManageProducts;
use Modules\Menu\Filament\Admin\Resources\Products\Schemas\ProductForm;
use Modules\Menu\Filament\Admin\Resources\Products\Tables\ProductTable;
use Modules\Menu\Models\Product;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Menu;

    public static function getPluralModelLabel(): string
    {
        return __('Position');
    }

    public static function getNavigationLabel(): string
    {
        return __('Positions');
    }

    protected function getTableQuery(): Builder
    {
        return Product::query()->with('childs', 'parent', 'category')->fastPaginate();
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProducts::route('/'),
        ];
    }
}
