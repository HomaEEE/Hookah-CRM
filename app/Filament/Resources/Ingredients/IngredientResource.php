<?php

namespace App\Filament\Resources\Ingredients;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Ingredients\Pages\ManageIngredients;
use App\Filament\Resources\IngredientResource\Pages;
use App\Models\Ingredient;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IngredientResource extends Resource
{
    protected static ?string $model = Ingredient::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Menu');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Ingredient');
    }

    public static function getNavigationLabel(): string
    {
        return __('Ingredients');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ingredient')->translateLabel(),
                Select::make('unit')->translateLabel()->options([
                    'l' => __('Liter'),
                    'kg' => __('Kilogram'),
                    'p' => __('Unit'),

                ]),
                TextInput::make('barcode'),
                TextInput::make('price')->translateLabel()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ingredient')->translateLabel()
                    ->searchable(['ingredient', 'barcode'])
                    ->size('sm')
                    ->description(fn (Ingredient $record): string => $record->barcode ?? ''),
                TextColumn::make('unit')->translateLabel()->badge(),
                TextColumn::make('price')->translateLabel()->money('UAH', 100),
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
            'index' => ManageIngredients::route('/'),
        ];
    }
}
