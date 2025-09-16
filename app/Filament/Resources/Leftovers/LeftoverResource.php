<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeftoverResource\Pages;
use App\Filament\Resources\LeftoverResource\RelationManagers;
use App\Models\Leftover;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeftoverResource extends Resource
{
    protected static ?string $model = Leftover::class;

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('Manage');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Leftovers');
    }

    public static function getNavigationLabel(): string
    {
        return __('Leftovers');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ingredient.ingredient')->label(__('Ingredient')),
                TextColumn::make('workshop.name')->label(__('Workshop')),
                TextColumn::make('qty')->label(__('Quantity')),
                TextColumn::make('updated_at')->label(__('Last update')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeftovers::route('/'),
            'create' => Pages\CreateLeftover::route('/create'),
            'edit' => Pages\EditLeftover::route('/{record}/edit'),
        ];
    }
}
