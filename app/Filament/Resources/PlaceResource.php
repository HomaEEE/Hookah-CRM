<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaceResource\Pages;
use App\Filament\Resources\PlaceResource\RelationManagers;
use App\Models\Place;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Resources\Concerns\Translatable;

use Filament\Infolists\Components;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Resources\RelationManagers\RelationGroup;

class PlaceResource extends Resource

{
    use Translatable;

    protected static ?string $tenantOwnershipRelationshipName = 'users';

    public static function getTranslatableLocales(): array
    {
        return ['ru', 'uk', 'en'];
    }

    protected static ?string $model = Place::class;

    protected static ?string $recordTitleAttribute = 'nameCity';

    protected static ?string $slug = 'places';

    protected static ?int $navigationSort = 80;


    public static function getPluralModelLabel(): string
    {
        return __('Place');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Places');
    }


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
                ->schema([
                    TextInput::make('name')->label(__('Place')),
                    TextInput::make('city')->label(__('City')),
                    TextInput::make('address')->label(__('Address')),
                    TextInput::make('phone')->label(__('Phone')),
                    TextInput::make('instagram')->label(__('Instagram')),
                    TextInput::make('slug')->label(__('Sufix for place')),
                    Toggle::make('opened')->inline(),
                ]),
            Section::make()
                ->schema([
                    KeyValue::make('time')->label(__('Working time'))
                        ->keyLabel(__('Day'))
                        ->valueLabel(__('Time')),
                    KeyValue::make('settings')->label(__('Settings')),
                    KeyValue::make('properties')->label(__('Properties')),
                    KeyValue::make('pays')->label(__('Pays')),
                ])->columnSpan(['lg' => 3]),
        ])->columns(3);
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
                    Tabs\Tab::make('Tab 1')
                        ->schema([
                            TextEntry::make('name'),
                            TextEntry::make('slug'),
                            TextEntry::make('opened'),
                        ]),
                        Tabs\Tab::make('Tab 2')
                        ->schema([
                            TextEntry::make('name'),
                            TextEntry::make('slug'),
                            TextEntry::make('opened'),
                            ])
                        ]),

            ]);
        }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StorageRelationManager::class,
            RelationManagers\WagesRelationManager::class,
            RelationManagers\WorkshopsRelationManager::class,
            RelationManagers\BillsRelationManager::class,
            RelationGroup::make(__('Room & Seets'), [
                RelationManagers\SeetsRelationManager::class,
                RelationManagers\RoomsRelationManager::class
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
