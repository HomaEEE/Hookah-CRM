<?php

namespace App\Filament\Resources\PlaceResource\RelationManagers;

use App\Enums\Bill as BillEnum;
use App\Enums\Currency;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class BillsRelationManager extends RelationManager
{
    protected static string $relationship = 'bills';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Mis Clientes';


    public static function getPluralModelLabel(): string
    {
        return __('Bills');
    }

    public static function getNavigationLabel(): string
    {
        return __('Bills');
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label(__('Bill name')),
                Select::make('currency')->label(__('Currency'))
                    ->options(Currency::class),
                Select::make('type')->label(__('Type bill'))
                    ->options(BillEnum::class),
                TextInput::make('balance')->label(__('Bill balance')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('currency')->badge(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('balance'),
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }
}
