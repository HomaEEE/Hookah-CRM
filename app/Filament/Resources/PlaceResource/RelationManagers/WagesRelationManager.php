<?php

namespace App\Filament\Resources\PlaceResource\RelationManagers;

use App\Models\Workshop;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Component as Livewire;

class WagesRelationManager extends RelationManager
{
    protected static string $relationship = 'wages';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {

        return $form
            ->schema([
                Hidden::make('place_id')->label(__('Place'))
                    ->default(fn (Livewire $livewire) => $livewire->ownerRecord->id),
                TextInput::make('name')->translateLabel()
                    ->required()
                    ->maxLength(255),
                TextInput::make('wage')->translateLabel()
                    ->required()
                    ->numeric()
                    ->maxLength(5),

                Repeater::make('procent')
                    ->schema([
                        Select::make('workshop_id')->translateLabel()
                            ->options(fn (Livewire $livewire) => Workshop::where('place_id', $livewire->ownerRecord->id)->get()->pluck('name', 'id')),
                        TextInput::make('num')->translateLabel()->numeric()
                            ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),

                    ]),
            ])->columns(1);

    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->translateLabel(),
                Tables\Columns\BadgeColumn::make('wage')->translateLabel(),
                Tables\Columns\TextColumn::make('procent.workshop_id')->translateLabel(),

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
