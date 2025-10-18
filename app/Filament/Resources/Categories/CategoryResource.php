<?php

namespace App\Filament\Resources\Categories;

use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;
use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?int $navigationSort = 2;

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('place_id')->default(Filament::getTenant()->id),

                TextInput::make('category_name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TranslatableTabs::make()
                    ->addEmptyBadgeWhenAllFieldsAreEmpty(emptyLabel: __('locale empty'))
                    ->schema([
                        TextInput::make('frontend_name')->label(__('Front category name')),
                        TextInput::make('frontend_menu_name')->label(__('Front menu')),
                    ]),
                KeyValue::make('class')->label(__('Custom class')),
                // ->default( fn (Livewire $livewire) =>  $livewire->ownerRecord->id),
                Select::make('parent_category')
                    ->label(__('Category'))
                    ->options(Category::where('place_id', Filament::getTenant()->id)->pluck('category_name', 'category_id'))
                    ->searchable(),
                TextInput::make('slug')->label(__('Slug'))->readOnly(),
                Toggle::make('visible')->label(__('Visible')),
                Toggle::make('visible_menu')->label(__('Visible menu')),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('category_name')
            ->defaultSort('sort_order', 'asc')
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
                    ->size(IconSize::Large)
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
                    ->sortable(),

            ])
            ->reorderable('sort_order')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}
