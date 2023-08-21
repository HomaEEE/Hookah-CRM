<?php

namespace App\Filament\Resources\ShiftResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpenseRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    protected static ?string $recordTitleAttribute = 'added_at';


    public function table(Table $table): Table
    {


        return $table
        ->modifyQueryUsing(fn (Builder $query) =>
            $query->where(
            'place_id', $this->ownerRecord->place_id)
                    ->whereDate('added_at', $this->ownerRecord->created_at ) )
            ->columns([
                TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
                ->size('sm')
                ->badge()
                ->description(fn ($record): string => $record->user->name ?? ''),

                TextColumn::make('category.name')->translateLabel(),
                TextColumn::make('sum')->translateLabel(),
                TextColumn::make('type_pay')->translateLabel(),
                TextColumn::make('pay')->translateLabel(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),

                // Tables\Actions\CreateAction::make(),
            ]);
        // ->actions([
        //     Tables\Actions\EditAction::make(),
        //     Tables\Actions\DetachAction::make(),
        //     Tables\Actions\DeleteAction::make(),
        // ])
        // ->bulkActions([
        //     Tables\Actions\DetachBulkAction::make(),
        //     Tables\Actions\DeleteBulkAction::make(),
        // ]);
    }
}
