<?php

namespace Modules\Menu\Filament\Admin\Resources\Products\Tables;

use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Menu\Models\Category;
use Modules\Menu\Models\Product;

class ProductTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordClasses(fn (Product $record) => match ($record->visibleMenu) {
                0 => 'border-1-4 |border-l-danger-500 bg-danger-50 dark:bg-gray-800',
                default => null,
            })
            ->recordTitleAttribute('product_name')
            ->columns([
                SpatieMediaLibraryImageColumn::make('media')
                    ->collection('products')
                    ->lightbox()
                    ->size(40),

                TextColumn::make('product_name')->searchable()->wrap()
                    ->description(fn (Product $record): string => $record->frontend_name ?? '', position: 'before')
                    ->description(fn (Product $record): string => $record->category['category_name'] ?? '', position: 'above'),

                TextColumn::make('cost')->label(__('cost'))->money('uah')->translateLabel()
                    ->searchable()
                    ->size(TextSize::Medium)

                    ->visibleFrom('md')
                    ->sortable(),

                TextColumn::make('price')->label(__('price'))->money('uah')->color('success')->translateLabel()
                    ->searchable()
                    ->size(TextSize::Medium)
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
                    ->size(IconSize::Medium)
                    ->boolean()
                    ->trueIcon(Phosphor::Eye)
                    ->falseIcon(Phosphor::EyeClosed)
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => ! $record->$name,
                        ]);
                    }),

                IconColumn::make('new')
                    ->label(__('New'))
                    ->size(IconSize::Medium)
                    ->boolean()
                    ->trueIcon(Phosphor::SealCheck)
                    ->falseIcon(Phosphor::Seal)

                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => ! $record->$name,
                        ]);
                    }),
            ])
            ->defaultPaginationPageOption(25)
            ->striped()
            ->filters([
                SelectFilter::make('menu_category_id')
                    ->label(__('Category'))
                    ->native(false)
                    ->multiple()
                    ->options(
                        Category::where('place_id', Filament::getTenant()->id)->pluck('category_name', 'category_id')
                    ),
                TernaryFilter::make('visible_menu')
                    ->label(__('Visible'))
                    ->nullable()
                    ->native(false)
                    ->placeholder(__('All'))
                    ->trueLabel(__('Visible'))
                    ->falseLabel(__('Hidden'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('visible_menu', 1),
                        false: fn (Builder $query) => $query->where('visible_menu', 0),
                        blank: fn (Builder $query) => $query, // In this example, we do not want to filter the query when it is blank.
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()->slideOver(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
