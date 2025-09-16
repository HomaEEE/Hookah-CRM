<?php

namespace App\Filament\Clusters\Settings\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Resources\PlaceResource\Pages;
use App\Filament\Clusters\Settings\Resources\PlaceResource\RelationManagers;
use App\Models\Place;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlaceResource extends Resource
{
    protected static ?string $cluster = Settings::class;

    protected static ?string $tenantOwnershipRelationshipName = 'users';

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $model = Place::class;

    protected static ?string $recordTitleAttribute = 'nameCity';

    protected static ?string $slug = 'places';

    protected static ?int $navigationSort = 1;

    public static function getPluralModelLabel(): string
    {
        return __('Place');
    }

    // public static function getNavigationGroup(): ?string
    // {
    //     return __('Settings');
    // }

    public static function getNavigationLabel(): string
    {
        return __('Places');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('Place'))->description(fn (Place $record): string => $record->city),
                TextColumn::make('phone')->label(__('Phone')),
                IconColumn::make('opened')
                    ->label(__('Opened'))
                    ->boolean()
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => ! $record->$name,
                        ]);
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Label')
                    ->tabs([
                        Tabs\Tab::make(__('Place'))
                            ->schema([
                                TextEntry::make('name')->translateLabel(),
                                TextEntry::make('city')->translateLabel(),
                                TextEntry::make('address')->translateLabel(),
                                TextEntry::make('phone')->translateLabel(),
                                TextEntry::make('instagram')->translateLabel(),
                                TextEntry::make('time')->translateLabel(),
                            ])->columns(2),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StorageRelationManager::class,
            RelationManagers\WagesRelationManager::class,
            RelationManagers\ExpenseCategoryRelationManager::class,
            RelationManagers\WorkshopsRelationManager::class,
            RelationManagers\BillsRelationManager::class,
            RelationGroup::make(__('Room & Seets'), [
                RelationManagers\SeetsRelationManager::class,
                RelationManagers\RoomsRelationManager::class,
            ]),
            RelationManagers\MetersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlaces::route('/'),
            'create' => Pages\CreatePlace::route('/create'),
            'edit' => Pages\EditPlace::route('/{record}/edit'),
            'view' => Pages\ViewPlace::route('/{record}'),

        ];
    }
}
