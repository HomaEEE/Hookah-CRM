<?php

namespace Modules\Menu\Livewire;

use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Modules\Menu\Models\Category;
use Modules\Menu\Models\Product;

class StopList extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query())
            ->columns([
                TextColumn::make('product_name')->searchable()->wrap()->label(__('Item'))
                    ->description(fn (Product $record): string => $record->frontend_name ?? '', position: 'before')
                    ->description(fn (Product $record): string => $record->category['category_name'] ?? '', position: 'above'),
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
            ->filters([

                TernaryFilter::make('visible_menu')
                    ->label(__('Visible'))
                    ->nullable()
                    ->native(false)
                    ->placeholder(__('All'))
                    ->trueLabel(__('Visibled'))
                    ->falseLabel(__('Hidden'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('visible_menu', 1),
                        false: fn (Builder $query) => $query->where('visible_menu', 0),
                        blank: fn (Builder $query) => $query, // In this example, we do not want to filter the query when it is blank.
                    ),                SelectFilter::make('menu_category_id')
                    ->label(__('Category'))
                    ->native(false)
                    ->multiple()
                    ->options(
                        Category::where('place_id', Filament::getTenant()->id)->pluck('category_name', 'category_id')
                    ),
            ])
            ->extremePaginationLinks()
            ->defaultSort('product_name', 'asc')

            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('menu::livewire.stop-list');
    }
}
