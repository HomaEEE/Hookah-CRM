<?php

namespace App\Filament\Resources\StorageResource\RelationManagers;

use App\Enums\Storage;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Joshembling\ImageOptimizer\Components\SpatieMediaLibraryFileUpload;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make('name')->translateLabel(),
                Tables\Columns\TextColumn::make('count')->translateLabel(),
                Tables\Columns\TextColumn::make('price')->translateLabel()->money('UAH', divideBy: 1),
                Tables\Columns\TextColumn::make('total')->translateLabel()->money('UAH', divideBy: 1),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name'),
                // другие поля
            ]);
    }
}
