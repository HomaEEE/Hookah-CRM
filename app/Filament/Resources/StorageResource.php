<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StorageResource\Pages;
use App\Filament\Resources\StorageResource\RelationManagers;
use App\Models\{Item, Storage, Workshop};
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StorageResource extends Resource
{
    protected static ?string $model = Storage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'id';


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
                Select::make('workshop_id')->translateLabel()
                    ->options(Workshop::where('place_id', Filament::getTenant()->id)->get()->pluck('name', 'id')),
                        Select::make('item_id')
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
                                    ])->columns(2),
                            ]),
                        TextInput::make('qty')->numeric(),
                        SpatieMediaLibraryFileUpload::make('item')
                        ->collection('storage')
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
                        Textarea::make('comment')->translateLabel()
                        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')->label(__('Title')),
                TextColumn::make('qty')->label(__('Quantity')),
                TextColumn::make('item.price')->label(__('Price')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStorages::route('/'),
        ];
    }
}
