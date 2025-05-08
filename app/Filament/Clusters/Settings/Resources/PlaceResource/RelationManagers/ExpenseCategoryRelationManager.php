<?php

namespace App\Filament\Clusters\Settings\Resources\PlaceResource\RelationManagers;

use App\Http\Controllers\PosterApiController;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenseCategoryRelationManager extends RelationManager
{
    protected static string $relationship = 'ExpenseCategories';

    public static function getPluralModelLabel(): string
    {
        return __('ExpenseCategories');
    }

    public static function getNavigationLabel(): string
    {
        return __('ExpenseCategories');
    }

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
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('poster_category_id'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('getCategories')
                    ->label(__('Get Categories'))
                    ->color('info')
                    ->icon('heroicon-o-arrow-down-circle')
                    ->slideOver()
                    ->modalHeading(__('Get Categories'))
                    ->requiresConfirmation()
                    ->action(function (): void {
                        (new PosterApiController)->getExpenseCategories($this->ownerRecord->id);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
