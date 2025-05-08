<?php

namespace App\Filament\Clusters\Settings\Resources\PlaceResource\RelationManagers;

use App\Http\Controllers\PosterApiController;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
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
                TextInput::make('field')->translateLabel()
                    ->required()
                    ->maxLength(20),
                Select::make('type')->translateLabel()
                    ->options([
                        'count' => __('Count'),
                        'revenue' => __('Revenue'),
                    ]),
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
                        (new PosterApiController)->getWorkshops($this->ownerRecord->id);
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
