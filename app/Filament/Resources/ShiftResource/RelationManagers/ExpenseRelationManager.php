<?php

namespace App\Filament\Resources\ShiftResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Contracts\Support\Htmlable;

class ExpenseRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    protected static ?string $recordTitleAttribute = 'added_at';

    public static function getNavigationLabel(): string
    {
        return __('Expenses');
    }

    protected function getTableHeading(): string | Htmlable
    {
        return __('Expenses of Shift');
    }

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
                TextColumn::make('category.name')->translateLabel()->badge()
                ->description(fn ($record): string => $record->supplier->company ?? ''),
                TextColumn::make('sum')->translateLabel()->moneY('UAH', true)->badge()
                ->summarize(Sum::make()->label(__('Total'))),
                IconColumn::make('bill_id')->label(__('Bill'))
                ->icon(fn (string $state): string => match ($state) {
                    '0' => 'heroicon-o-banknotes',
                    '1' => 'heroicon-o-credit-card',
                })
                ->color(fn (string $state): string => match ($state) {
                    '1' => 'warning',
                    '0' => 'success',
                    default => 'gray',
                }),
                IconColumn::make('pay')->translateLabel()
               ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
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
