<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Livewire;
use SolutionForest\FilamentTranslateField\Forms\Component\Translate;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('Menu');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Category');
    }

    public static function getNavigationLabel(): string
    {
        return __('Categories');
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('category_name')
                ->required()
                ->maxLength(255),
                Translate::make()
                ->schema([
                    TextInput::make('frontend_name')->label(__('Front category name')),
                    TextInput::make('frontend_menu_name')->label(__('Front menu')),
                ]),
            KeyValue::make('class')->label(__('Custom class')),
            // ->default( fn (Livewire $livewire) =>  $livewire->ownerRecord->id),
            Select::make('parent_category')
                ->label(__('Category'))
                ->options( Category::where('place_id', Filament::getTenant()->id)->pluck('category_name', 'category_id'))
                ->searchable(),
            TextInput::make('slug')->label(__('Slug')),
            Toggle::make('visible')->label(__('Visible')),
            Toggle::make('visible_menu')->label(__('Visible menu')),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordTitleAttribute('category_name')
        ->columns([
            TextColumn::make('category_name')
                ->translateLabel()
                ->searchable()
                ->sortable()
                ->description(fn (Category $record): string => $record->frontend_name ?? '', position: 'before'),
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
            'index' => Pages\ManageCategories::route('/'),
        ];
    }
}
