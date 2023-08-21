<?php

namespace App\Filament\Resources\PlaceResource\RelationManagers;

use App\Enums\Meter as MeterEnum;
use App\Models\Place;
use App\Models\Room;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component as Livewire;

class MetersRelationManager extends RelationManager
{
    protected static string $relationship = 'meters';

    protected static ?string $recordTitleAttribute = 'Meter';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('room_id')->label(__('Room'))
                    ->relationship('room', 'room', fn (Builder $query, Livewire $livewire) => $query->where('place_id', $livewire->ownerRecord->id))
                    ->preload(),
                // ->options( fn (Livewire $livewire) =>  Room::where('place_id', $livewire->ownerRecord->id)->pluck('room', 'id') ),
                Select::make('type')
                    ->label(__('Type'))
                    ->options(MeterEnum::$TYPE)
                    ->preload(),
                TextInput::make('price')->label(__('Tarif')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('room.roomName')->label(__('Room')),
                BadgeColumn::make('type')->label(__('Type meter'))
                    ->enum(MeterEnum::$TYPE)
                    ->colors([
                        'primary' => 0,
                        'success' => 1,
                    ]),
                TextColumn::make('price')
                    ->label(__('Tarif'))->money('uah', true)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
