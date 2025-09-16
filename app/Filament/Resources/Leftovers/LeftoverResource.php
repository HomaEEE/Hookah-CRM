<?php

namespace App\Filament\Resources\Leftovers;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Leftovers\Pages\ListLeftovers;
use App\Filament\Resources\Leftovers\Pages\CreateLeftover;
use App\Filament\Resources\Leftovers\Pages\EditLeftover;
use App\Filament\Resources\LeftoverResource\Pages;
use App\Filament\Resources\LeftoverResource\RelationManagers;
use App\Models\Leftover;
use Filament\Forms;
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
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListLeftovers::route('/'),
            'create' => CreateLeftover::route('/create'),
            'edit' => EditLeftover::route('/{record}/edit'),
        ];
    }
}
