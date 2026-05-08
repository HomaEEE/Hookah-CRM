<?php

namespace App\Filament\Resources\Purchases;

use App\Enums\IngredientUnit;
use App\Enums\NavigationGroup;
use App\Filament\Resources\Purchases\Pages\ManagePurchases;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\Supplier;
use Auth;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Manage;

    public static function getPluralModelLabel(): string
    {
        return __('Purchase');
    }

    public static function getNavigationLabel(): string
    {
        return __('Purchases');
    }

    public function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-view-grid-add';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                Hidden::make('user_id')->default(Auth::user()->id),
                Select::make('supplier_id')->label(__('Supplier'))
                    ->native(false)
                    ->options(
                        Supplier::where('place_id', Filament::getTenant()->id)->visible()->pluck('company', 'id')
                    )
                    ->required(),

                Repeater::make('ingredients')->label(__('Ingredients'))
                    ->columns(12)
                    ->addActionLabel(__('Add'))
                    ->schema([
                        Select::make('ingredient_id')
                            ->hiddenLabel()
                            ->preload()
                            ->native(false)
                            ->searchable()
                            ->columnSpan(7)
                            ->live()
                            ->options(
                                Ingredient::where('place_id', Filament::getTenant()->id)->pluck('ingredient', 'id')
                            ),
                        TextInput::make('quantity')
                            ->live()
                            ->columnSpan(5)
                            ->hiddenLabel()
                            ->suffix(static function (Get $get) {

                                if ($get('ingredient_id') != null) {
                                    $ingredient = Ingredient::find($get('ingredient_id'));
                                    if ($ingredient && $ingredient->unit instanceof IngredientUnit) {
                                        return $ingredient->unit->getLabel();
                                    }
                                }

                                return null;

                            }),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier.company')->label(__('Supplier')),
                TextColumn::make('status')->translateLabel(),
                TextColumn::make('created_at')->translateLabel()->dateTime('d/m/Y')->badge(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePurchases::route('/'),
        ];
    }
}
