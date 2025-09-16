<?php

namespace App\Filament\Clusters\Settings\Resources\Places\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use App\Http\Controllers\PosterApiController;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                TextColumn::make('name')->label(__('Workshop')),
                TextColumn::make('workshop_id')->label(__('ID')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('getWorkshop')
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
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
