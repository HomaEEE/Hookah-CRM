<?php

namespace App\Filament\Clusters\Settings\Resources\Places\RelationManagers;

use App\Models\Room;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class SeetsRelationManager extends RelationManager
{
    protected static string $relationship = 'seets';

    protected static ?string $recordTitleAttribute = 'table_id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')->label(__('Table_id')),
                Select::make('room_id')->label(__('Room'))
                    ->options(Room::where('place_id', Filament::getTenant()->id)->pluck('room', 'id')),
                Hidden::make('place_id')->label(__('Place'))
                    ->default(Filament::getTenant()->id),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('room_id')
                    ->label(__('Room')),
            ])
            ->columns([
                TextColumn::make('number')->translateLabel(),
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }
}
