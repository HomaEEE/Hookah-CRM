<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IngredientResource\Pages;
use App\Filament\Resources\IngredientResource\RelationManagers;
use App\Models\Ingredient;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ingredient')->translateLabel(),
                Select::make('unit')->translateLabel()->options([
                    'l' => __('Liter'),
                    'kg' => __('Kilogram'),
                    'p' => __('Unit'),

                ]),
                TextInput::make('price')->translateLabel()
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->numeric()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ingredient')->translateLabel(),
                TextColumn::make('unit')->translateLabel(),
                TextColumn::make('price')->translateLabel()->money('UAH', divideBy: 100)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->mutateRecordDataUsing(function (array $data): array {
                    $data['price'] =  $data['price']/100;
                    return $data;
                })
                ->mutateFormDataUsing(function (array $data): array {
                    $data['price'] =  $data['price']*100;
                    return $data;
                }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageIngredients::route('/'),
        ];
    }
}
