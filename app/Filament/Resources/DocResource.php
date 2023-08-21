<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocResource\Pages;
use App\Models\Doc;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

use Filament\Tables;
use Filament\Tables\Table;


use Auth;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;

class DocResource extends Resource
{
    protected static ?string $model = Doc::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    public static function getPluralModelLabel(): string
    {
        return __('Docs');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Data');
    }

    public static function getNavigationLabel(): string
    {
        return __('Docs');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('place_id')->label(__('Place'))
                ->options(
                    Auth::user()->place->pluck('nameCity', 'id')
                )->required(),
                TextInput::make('name')->label(__('Name'))->required(),
                Select::make('type')->label(__('Type'))
                ->options([
                    '1' => __('Pass'),
                    '2' => __('License'),
                    '3' => __('EDR'),
                    '4' => __('Key'),
                ])->required(),
                DatePicker::make('date')->label(__('Date Expired')),
                SpatieMediaLibraryFileUpload::make('invoice')
                        ->collection('supplies')
                        ->multiple()
                        ->maxFiles(10)
                        ->imagePreviewHeight('250')
                        ->imageResizeMode('contain')
                        ->openable()
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('place.city')->translateLabel(),
                TextColumn::make('name')->translateLabel(),
                TextColumn::make('type')->translateLabel()
                ->formatStateUsing(
                    fn (string $state): string => match ($state) {
                        '1' => __('Pass'),
                        '2' => __('License'),
                        '3' => __('EDR'),
                        '4' => __('Key'),
                    }),
                TextColumn::make('date')->dateTime('d/m/Y')->label(__('Date Expired')),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDocs::route('/'),
        ];
    }
}
