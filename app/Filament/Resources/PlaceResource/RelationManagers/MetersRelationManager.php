<?php

namespace App\Filament\Resources\PlaceResource\RelationManagers;

use App\Enums\MeterType;
use App\Models\Place;
use App\Models\Room;
use Filament\Facades\Filament;
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

    public static function getPluralModelLabel(): string
    {
        return __('Meters');
    }

    public static function getNavigationLabel(): string
    {
        return __('Meters');
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('room_id')->label(__('Room'))
                    ->relationship('room', 'room')
                    ->options( Room::where('place_id', Filament::getTenant()->id)->pluck('room', 'id')),
                Select::make('type')
                    ->label(__('Type'))
                    ->options(MeterType::class),
                TextInput::make('price')->label(__('Tarif'))->inputMode('decimal')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('room.roomName')->label(__('Room')),
                TextColumn::make('type')->label(__('Type meter'))->badge(),
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
