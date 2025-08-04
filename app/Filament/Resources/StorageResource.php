<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StorageResource\Pages;
use App\Filament\Resources\StorageResource\RelationManagers;
use App\Filament\Resources\StorageResource\RelationManagers\ItemsRelationManager;
use App\Models\Storage;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;

class StorageResource extends Resource
{
    protected static ?string $model = Storage::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('Manage');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Storage');
    }

    public static function getNavigationLabel(): string
    {
        return __('Storage');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                TextInput::make('name'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('Title'))->searchable()
                    ->description(fn (Storage $record): string => $record->comment ?? ''),
                TextColumn::make('totalItems')->label(__('Quantity')),
                TextColumn::make('TotalSum')->label(__('Sum'))->money('UAH', divideBy: 1),
            ])
            ->allowDuplicates()
            ->filters([
                //
            ])
            ->actions([
                RelationManagerAction::make('lesson-relation-manager')
                    ->label(__('Items list'))
                    ->relationManager(ItemsRelationManager::make()),
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStorages::route('/'),
        ];
    }
}
