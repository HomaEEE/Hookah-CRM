<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Actions\ActionGroup;

use Filament\Infolists\Infolist;

use Livewire\Component as Livewire;

class CategoryRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';

    public static function getPluralModelLabel(): string
    {
        return __('Categories');
    }

    public static function getNavigationLabel(): string
    {
        return __('Categories');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['category_name', 'frontend_name'];
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('category_name')
                    ->required()
                    ->maxLength(255),
                KeyValue::make('frontend_name')->label(__('Front category name')),
                KeyValue::make('frontend_menu_name')->label(__('Front menu')),
                KeyValue::make('class')->label(__('Custom class')),
                // ->default( fn (Livewire $livewire) =>  $livewire->ownerRecord->id),
                Select::make('parent_category')
                    ->label(__('Category'))
                    ->options(fn (Livewire $livewire) => Category::where('place_id', $livewire->ownerRecord->id)->pluck('category_name', 'category_id'))
                    ->searchable(),
                TextInput::make('slug')->label(__('Slug')),
                Toggle::make('visible')->label(__('Visible')),
                Toggle::make('visible_menu')->label(__('Visible menu')),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            TextEntry::make('frontend_name'),
            TextEntry::make('frontend_menu_name'),
            TextEntry::make('parent.category_name')
        ]);
}

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('category_name')
            ->columns([
                TextColumn::make('category_name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->description(fn (Category $record): string => $record->frontend_name, position: 'before'),
                TextColumn::make('parent.category_name')->translateLabel()->visibleFrom('md'),

                // IconColumn::make('visible')
                // ->label(__('Visible'))
                // ->size(IconColumn\IconColumnSize::Medium)
                // ->boolean()
                // ->trueIcon('heroicon-o-eye')
                // ->falseIcon('heroicon-o-eye-slash')
                //  ->action(function ($record, $column) {
                //      $name = $column->getName();
                //      $record->update([
                //          $name => ! $record->$name,
                //      ]);
                //  }),


             IconColumn::make('visible_menu')
                ->translateLabel()
                ->size(IconColumn\IconColumnSize::Large)
                 ->boolean()
                 ->sortable()
                 ->trueIcon('heroicon-o-eye')
                 ->falseIcon('heroicon-o-eye-slash')
                  ->action(function ($record, $column) {
                      $name = $column->getName();
                      $record->update([
                          $name => ! $record->$name,
                      ]);
                  }),

                TextColumn::make('sort_order')->label(__('Sorting'))->visibleFrom('md')
                ->sortable()


            ])
            ->reorderable('sort_order')
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
                    Tables\Actions\DeleteAction::make(),
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
