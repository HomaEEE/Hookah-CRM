<?php

namespace Modules\Finance\Filament\Admin\Resources\Incomes;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Finance\Filament\Admin\Resources\Incomes\Pages\ManageIncomes;
use Modules\Finance\Filament\Admin\Resources\Incomes\Schemas\IncomeForm;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    public static function getPluralModelLabel(): string
    {
        return __('Income');
    }

    public static function getNavigationLabel(): string
    {
        return __('Income');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

    public static function form(Schema $schema): Schema
    {
        return IncomeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label(__('Date Added'))->dateTime('d/m/Y')
                    ->size('md')
                    ->badge(),
                //     ->description(fn (Income $record): string => $record->user->name ?? ''),

                TextColumn::make('sum')->label(__('Sum'))->money('UAH')->badge(),
                TextColumn::make('comment')->translateLabel(),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()->slideOver(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageIncomes::route('/'),
        ];
    }
}
