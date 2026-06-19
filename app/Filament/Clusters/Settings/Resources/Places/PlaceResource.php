<?php

namespace App\Filament\Clusters\Settings\Resources\Places;

use App\Filament\Clusters\Settings\Resources\Places\Pages\CreatePlace;
use App\Filament\Clusters\Settings\Resources\Places\Pages\EditPlace;
use App\Filament\Clusters\Settings\Resources\Places\Pages\ListPlaces;
use App\Filament\Clusters\Settings\Resources\Places\Pages\ViewPlace;
use App\Filament\Clusters\Settings\Resources\Places\RelationManagers\BillsRelationManager;
use App\Filament\Clusters\Settings\Resources\Places\RelationManagers\ExpenseCategoryRelationManager;
use App\Filament\Clusters\Settings\Resources\Places\RelationManagers\RoomsRelationManager;
use App\Filament\Clusters\Settings\Resources\Places\RelationManagers\SeetsRelationManager;
use App\Filament\Clusters\Settings\Resources\Places\RelationManagers\StorageRelationManager;
use App\Filament\Clusters\Settings\Resources\Places\RelationManagers\WagesRelationManager;
use App\Filament\Clusters\Settings\Resources\Places\RelationManagers\WorkshopsRelationManager;
use App\Filament\Clusters\Settings\SettingsCluster;
use App\Models\Place;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlaceResource extends Resource
{
    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $tenantOwnershipRelationshipName = 'users';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

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
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Label')
                    ->tabs([
                        Tab::make(__('Place'))
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
            StorageRelationManager::class,
            WagesRelationManager::class,
            ExpenseCategoryRelationManager::class,
            WorkshopsRelationManager::class,
            BillsRelationManager::class,
            RelationGroup::make(__('Room & Seets'), [
                SeetsRelationManager::class,
                RoomsRelationManager::class,
            ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlaces::route('/'),
            'create' => CreatePlace::route('/create'),
            'edit' => EditPlace::route('/{record}/edit'),
            'view' => ViewPlace::route('/{record}'),

        ];
    }
}
