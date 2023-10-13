<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Models\{Category,Product};
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\ImageColumn;
use Filament\Resources\RelationManagers\Concerns\Translatable;

use Livewire\Component as Livewire;

class ProductRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public static function getGloballySearchableAttributes(): array
    {
        return ['product_name', 'category.category_name', 'price'];
    }

    public static function getPluralModelLabel(): string
    {
        return __('Items');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('visible')->label(__('Visible'))->inline(),
                Toggle::make('visible_menu')->label(__('Visible menu'))->inline(),
                TextInput::make('product_name')
                    ->required()
                    ->maxLength(255),
                Select::make('menu_category_id')
                    ->label(__('Category'))
                    ->options(fn (Livewire $livewire) => Category::where('place_id', $livewire->ownerRecord->id)->pluck('category_name', 'category_id'))
                    ->searchable(),
                KeyValue::make('frontend_name')->label(__('Front product name')),
                KeyValue::make('frontend_desc')->label(__('Description')),

                TextInput::make('sup')->label(__('sup')),
                TextInput::make('barcode')->label('Barcode'),

                TextInput::make('out')->label(__('Out'))->numeric()
                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                TextInput::make('price')->label(__('Price'))->numeric()
                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                    ->required(),
                Textarea::make('ingredients')->label(__('Ingredients'))
                    ->visible(fn ($record) => ! empty($record['ingredients']) ? '1' : '0'),

                    SpatieMediaLibraryFileUpload::make('media')
                    ->collection('products')
                    ->image()
                    ->imagePreviewHeight('400')
                    ->imageResizeTargetWidth('400')
                    ->imageResizeMode('contain')
                    // ->imageResizeMode('contain')
                    // ->imageResizeTargetHeight('400')

            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            TextEntry::make('frontend_name')->translateLabel(),
            TextEntry::make('price')->translateLabel(),
            TextEntry::make('out')->translateLabel(),
            TextEntry::make('frontend_desc')->translateLabel(),
            ImageEntry::make('media')
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product__name')
            ->columns([
                SpatieMediaLibraryImageColumn::make('media')
                    ->collection('products')
                    ->circular()->size(50),

                TextColumn::make('product_name')->searchable()->wrap()
                    ->description(fn (Product $record): string => $record->frontend_name ?? '', position: 'before')
                    ->description(fn (Product $record): string => $record->category['category_name'] ?? '', position: 'above'),

                TextColumn::make('cost')->money('uah', true)->translateLabel()
                    ->searchable()
                    ->visibleFrom('md')
                    ->sortable(),
                TextColumn::make('price')->money('uah', true)->translateLabel()
                    ->searchable()
                    ->sortable(),
                // IconColumn::make('visible')
                //    ->label(__('Visible'))
                //    ->size(IconColumn\IconColumnSize::Medium)
                //    ->boolean()
                //    ->trueIcon('heroicon-o-eye')
                //    ->falseIcon('heroicon-o-eye-slash')
                //     ->action(function ($record, $column) {
                //         $name = $column->getName();
                //         $record->update([
                //             $name => ! $record->$name,
                //         ]);
                //     }),


                IconColumn::make('visible_menu')
                    ->label(__('Visible menu'))
                    ->size(IconColumn\IconColumnSize::Medium)
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                     ->action(function ($record, $column) {
                         $name = $column->getName();
                         $record->update([
                             $name => ! $record->$name,
                         ]);
                     })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ])
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
}
