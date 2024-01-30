<?php

namespace App\Filament\Resources;

use App\Enums\Storage as EnumsStorage;
use App\Filament\Resources\StorageResource\Pages;
use App\Filament\Resources\StorageResource\RelationManagers;
use App\Models\{Item, Storage, Workshop};
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Select::make('workshop_id')->label(__('Workshop'))
                    ->options(Workshop::where('place_id', Filament::getTenant()->id)->get()->pluck('name', 'id')),
                        Select::make('item_id')->label(__('Item'))
                            ->relationship('item', 'name')
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->required()
                            ->options(Item::pluck('name', 'id'))
                            ->createOptionForm([
                                Section::make()
                                    ->schema([
                                        TextInput::make('name')->translateLabel(),
                                        TextInput::make('price')->translateLabel(),
                                        SpatieMediaLibraryFileUpload::make('item')
                                        ->collection('storage')
                                        ->label(__('Photo'))
                                        ->imagePreviewHeight(350)
                                        ->imageResizeMode('cover')
                                        ->imageResizeTargetWidth('900')
                                        ->imageEditor()
                                        ->optimize('webp')
                                        ->imageEditorAspectRatios([
                                            null,
                                            '16:9',
                                            '4:3',
                                            '1:1',
                                        ])
                                        ->openable(),
                                    ])->columns(2),
                            ]),
                        Select::make('own')->translateLabel()->options(EnumsStorage::class),
                        TextInput::make('qty')->numeric()->label(__('Quantity')),
                        Textarea::make('comment')->translateLabel()
                        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')->label(__('Title'))->searchable()
                ->description(fn (Storage $record): string => $record->comment ?? ''),
                SpatieMediaLibraryImageColumn::make('item.item')->label(__('Photo'))
                    ->collection('storage')
                    ->simpleLightbox()
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->height(30)
                    ->toggleable()
                    ->defaultImageUrl(url('/assets/imgs/app/no-photo.png'))
                    ->placeholder('No description.')
                    ->visibleFrom('md'),
                TextColumn::make('own')->translateLabel()->badge(),
                TextColumn::make('qty')->label(__('Quantity')),
                TextColumn::make('item.price')->label(__('Price'))->searchable(),
            ])
            ->allowDuplicates()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(''),
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
