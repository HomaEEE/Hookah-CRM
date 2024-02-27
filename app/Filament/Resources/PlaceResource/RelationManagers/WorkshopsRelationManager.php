<?php

namespace App\Filament\Resources\PlaceResource\RelationManagers;

use App\Http\Controllers\PosterApiController;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Component as Livewire;

class WorkshopsRelationManager extends RelationManager
{
    protected static string $relationship = 'workshops';

    protected static ?string $recordTitleAttribute = 'Workshops';

    public static function getPluralModelLabel(): string
    {
        return __('Workshops');
    }

    public static function getNavigationLabel(): string
    {
        return __('Workshops');
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('place_id')->label(__('Place'))
                    ->default(fn (Livewire $livewire) => $livewire->ownerRecord->id),
                TextInput::make('name')->label(__('Workshop'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('procent')->translateLabel()
                    ->required()
                    ->maxLength(5)
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Workshop')),
                Tables\Columns\TextColumn::make('workshop_id')->label(__('ID')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('getWorkshop')
                ->label(__('Get Workshops'))
                ->color('info')
                ->icon('heroicon-o-arrow-down-circle')
                ->slideOver()
                ->modalHeading(__('Get Workshops'))
                ->requiresConfirmation()
                ->action(function (): void {
                    (new PosterApiController())->getWorkshops(Filament::getTenant()->id);
                }),
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
