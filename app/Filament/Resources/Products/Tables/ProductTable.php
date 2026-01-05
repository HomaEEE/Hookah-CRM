<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Category;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordClasses(fn (Product $record) => match ($record->visibleMenu) {
                0 => 'border-1-4 |border-l-danger-500 bg-danger-50 dark:bg-gray-800',
                default => null,
            })
            ->recordTitleAttribute('product__name')
            ->columns([
                SpatieMediaLibraryImageColumn::make('media')
                    ->collection('products')
                    ->simpleLightbox()
                    ->circular()
                    ->size(40),

                TextColumn::make('product_name')->searchable()->wrap()
                    ->description(fn (Product $record): string => $record->frontend_name ?? '', position: 'before')
                    ->description(fn (Product $record): string => $record->category['category_name'] ?? '', position: 'above'),

                TextColumn::make('cost')->money('uah')->translateLabel()
                    ->searchable()
                    ->badge('info')
                    ->visibleFrom('md')
                    ->sortable(),

                TextColumn::make('rrc')->money('uah')->color('info')->translateLabel(),
                TextColumn::make('price')->money('uah')->color('success')->translateLabel()
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
                    ->size(IconSize::Medium)
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->extraAttributes(function (Product $product) {
                        if ($product->visible_menu === 0) {
                            return ['class' => 'bg-danger-300 dark:bg-danger-600'];
                        }

                        return [];
                    })
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
