<?php

namespace App\Filament\Resources\ShiftResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Sum;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'shift_users';

    protected static ?string $recordTitleAttribute = 'user_id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->translateLabel(),
                TextColumn::make('days')->translateLabel(),
                TextColumn::make('wage')->translateLabel()->summarize(Sum::make()->label(__('Wage'))),
                TextColumn::make('procent_day')->translateLabel()->summarize(Sum::make()->label(__('Procent'))),
                TextColumn::make('cashout')->translateLabel()->summarize(Sum::make()->label(__('Total'))),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DetachAction::make(),
        ]);
        // ->bulkActions([
        //     Tables\Actions\DetachBulkAction::make(),
        //     Tables\Actions\DeleteBulkAction::make(),
        // ]);
    }
}
