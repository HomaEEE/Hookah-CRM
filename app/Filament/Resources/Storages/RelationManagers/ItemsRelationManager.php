<?php

namespace App\Filament\Resources\Storages\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Enums\Storage;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Joshembling\ImageOptimizer\Components\SpatieMediaLibraryFileUpload;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                Section::make()->schema([

                    TextInput::make('name')->translateLabel(),
                    TextInput::make('price')->translateLabel(),
                    TextInput::make('count')->numeric()->label(__('Quantity')),
                ])->columns(3)->columnSpan('full'),

                TextInput::make('sn')->translateLabel(),
                Select::make('own')->translateLabel()->options(Storage::class),
                Textarea::make('comment')->translateLabel()->rows(3),

                SpatieMediaLibraryFileUpload::make('item')
                    ->collection('storage')
                    ->label(__('Photo'))
                    ->imagePreviewHeight(350)
                    ->imageResizeMode('cover')
                    ->imageResizeTargetWidth('900')
                    ->imageEditor()
                    ->optimize('webp')
                    ->openable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Item')
            ->columns([
                TextColumn::make('name')->translateLabel(),
                TextColumn::make('count')->translateLabel(),
                TextColumn::make('price')->translateLabel()->money('UAH', divideBy: 1),
                TextColumn::make('total')->translateLabel()->money('UAH', divideBy: 1),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make()->label(''),
                EditAction::make()->label(''),
                DeleteAction::make()->label(''),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                // другие поля
            ]);
    }
}
