<?php

namespace App\Filament\Resources;

use App\Filament\Components\IncomeForm;
use App\Filament\Resources\IncomeResource\Pages;
use App\Models\Income;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static ?int $navigationSort = 2;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                IncomeForm::schema()
            );
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
            ->deferLoading()
            ->defaultPaginationPageOption(25)
            ->striped()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageIncomes::route('/'),
        ];
    }
}
