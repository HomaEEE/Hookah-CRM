<?php

namespace App\Filament\Clusters\Settings\PlaceResource\RelationManagers;

use App\Models\Workshop;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WagesRelationManager extends RelationManager
{
    protected static string $relationship = 'wages';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getPluralModelLabel(): string
    {
        return __('Wages');
    }

    public static function getNavigationLabel(): string
    {
        return __('Wages');
    }

    public function form(Form $form): Form
    {

        return $form
            ->schema([
                Hidden::make('place_id')->label(__('Place'))
                    ->default(Filament::getTenant()->id),

                TextInput::make('name')->translateLabel()
                    ->required()
                    ->maxLength(255),

                TextInput::make('wage')->translateLabel()
                    ->required()
                    ->numeric()
                    ->maxLength(5),

                Repeater::make('procent')
                    ->translateLabel()
                    ->schema([
                        Select::make('workshop_id')->label(__('Workshop'))
                            ->native(false)
                            ->options(Workshop::where('place_id', $this->ownerRecord->id)->get()->pluck('name', 'workshop_id')),
                        TextInput::make('num')->translateLabel()->numeric()
                            ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                    ]),

                TextInput::make('procent')
                    ->visible(fn (Get $get) => $get('type') == '2')
                    ->translateLabel(),
            ])->columns(1);

    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->translateLabel(),
                TextColumn::make('wage')->translateLabel()->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
