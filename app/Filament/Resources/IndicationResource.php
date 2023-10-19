<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IndicationResource\Pages;
use App\Filament\Resources\IndicationResource\RelationManagers;
use App\Models\{Indication,Meter,Room};
use App\Enums\MeterType;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IndicationResource extends Resource
{
    protected static ?string $model = Indication::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    public static function getNavigationGroup(): ?string
    {
        return __('Place');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Indications');
    }

    public static function getNavigationLabel(): string
    {
        return __('Indications');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TableRepeater::make('data')
                ->headers(['Meter', 'Indication'])
                ->emptyLabel('Add ingredients to techcard')
                ->schema([
                    Select::make('meter_id')->label(__('Meter'))
                    ->relationship('meter', 'id')
                    ->options(
                        Meter::where('place_id', Filament::getTenant()->id)->pluck('room', 'id')
                    ),
                    TextInput::make('indication')
                ])->columnSpan('full')

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageIndications::route('/'),
        ];
    }
}
