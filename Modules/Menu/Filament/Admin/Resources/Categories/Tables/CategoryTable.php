<?php

namespace Modules\Menu\Filament\Admin\Resources\Categories\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Kisame76\FilamentTreeTable\ExpandableRows;
use Modules\Menu\Models\Category;

class CategoryTable
{
    public static function configure(Table $table): Table
    {
        return ExpandableRows::make()
            ->parentKey('parent_id')           // default
            ->childrenRelationship('children') // default
            ->applyTo(
                $table->columns([
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
                    ->recordTitleAttribute('category_name')
                    ->defaultSort('sort_order', 'asc')
                    ->reorderable('sort_order')
                    ->recordActions([
                        ActionGroup::make([
                            ViewAction::make(),
                            EditAction::make(),
                            DeleteAction::make(),
                        ]),
                    ])
                    ->toolbarActions([
                        BulkActionGroup::make([
                            DeleteBulkAction::make(),
                        ]),
                    ])

            );
    }
}
