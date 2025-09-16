<?php

namespace App\Filament\Clusters\Settings\Resources\Places\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use App\Models\Workshop;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

    public function form(Schema $schema): Schema
    {

        return $schema
            ->components([
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
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
