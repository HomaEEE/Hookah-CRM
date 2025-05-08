<?php

namespace App\Filament\Clusters\Settings\Resources\PlaceResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    protected static ?string $recordTitleAttribute = 'room';

    public static function getPluralModelLabel(): string
    {
        return __('Rooms');
    }

    public static function getNavigationLabel(): string
    {
        return __('Rooms');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('room')->required(),
                TextInput::make('tags'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('room'),
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }
}
