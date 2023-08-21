<?php

namespace App\Filament\Resources\PlaceResource\RelationManagers;

use App\Models\Room;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Component as Livewire;

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
                    ->options(fn (Livewire $livewire) => Room::where('place_id', $livewire->ownerRecord->id)->pluck('room', 'id')),
                Hidden::make('place_id')->label(__('Place'))
                    ->default(fn (Livewire $livewire) => $livewire->ownerRecord->id),
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
