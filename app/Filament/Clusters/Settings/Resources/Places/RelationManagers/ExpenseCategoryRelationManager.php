<?php

namespace App\Filament\Clusters\Settings\Resources\Places\RelationManagers;

use App\Http\Controllers\PosterApiController;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                TextColumn::make('poster_category_id'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('getCategories')
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
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
