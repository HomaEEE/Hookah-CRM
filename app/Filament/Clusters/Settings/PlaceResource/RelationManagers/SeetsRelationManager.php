<?php

namespace App\Filament\Clusters\Settings\PlaceResource\RelationManagers;

use App\Models\Room;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SeetsRelationManager extends RelationManager
{
    protected static string $relationship = 'seets';

    protected static ?string $recordTitleAttribute = 'table_id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('table_id')->label(__('Table_id')),
                Select::make('room_id')->label(__('Room'))
                    ->options(Room::where('place_id', Filament::getTenant()->id)->pluck('room', 'id')),
                Hidden::make('place_id')->label(__('Place'))
                    ->default(Filament::getTenant()->id),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('table_id')->translateLabel(),
                Tables\Columns\TextColumn::make('room.roomName')->translateLabel(),
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
