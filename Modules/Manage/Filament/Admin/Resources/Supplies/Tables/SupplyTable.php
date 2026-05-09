<?php

namespace Modules\Manage\Filament\Admin\Resources\Supplies\Tables;

use App\Filament\Components\Bulks\CheckAsPaidAction;
use App\Filament\Components\Bulks\CheckPosterAction;
use App\Models\Supplier;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Modules\Finance\Models\Bill;
use Modules\Manage\Models\Supply;

class SupplyTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('created_at')->date()
                    ->label(__('Added at')),
            ])
            ->columns([
                TextColumn::make('id')->label(__('#'))->searchable()->visibleFrom('xl'),
                TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
                    ->badge()
                    ->size('sm')
                    ->description(fn (Supply $record): string => $record->user->name ?? ''),

                TextColumn::make('supplier.company')->label(__('Supplier'))->searchable()
                    ->description(fn (Supply $record): string => $record->comment ?? '')->wrap(),

                TextColumn::make('sum')->label(__('Sum'))->money('UAH')->searchable()
                    ->summarize(Sum::make()->money('UAH', 100)->label(__('Total'))),

                SpatieMediaLibraryImageColumn::make('invoice')->label(__('Invoice'))
                    ->collection('supplies')
                    ->lightbox()
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->height(40)
                    ->defaultImageUrl(url('/assets/imgs/app/no-photo.png'))
                    ->placeholder('No photo'),

                IconColumn::make('bill.type')->label(__('Bill'))
                    ->action(function ($record) {
                        $record->update([
                            'bill_id' => ! $record->bill_id,
                        ]);
                    }),

                IconColumn::make('pay')
                    ->translateLabel()
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => ! $record->$name,
                        ]);
                    })
                    ->visibleFrom('md'),
                IconColumn::make('poster')
                    ->translateLabel()
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => ! $record->$name,
                        ]);
                    })
                    ->visibleFrom('md'),

            ])->defaultSort('added_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->filters([

                DateRangeFilter::make('created_at')->translateLabel()
                    ->defaultLast30Days(),

                SelectFilter::make('supplier_id')
                    ->multiple()
                    ->label(__('Supplier'))
                    ->native(false)
                    ->options(
                        Supplier::where('place_id', Filament::getTenant()->id)->visible(1)->pluck('company', 'id')
                    ),

                SelectFilter::make('user')->translateLabel()
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                    )->attribute('user_id'),

                SelectFilter::make('bill')->translateLabel()
                    ->native(false)
                    ->options(Bill::get()->pluck('name', 'id'))
                    ->attribute('bill_id'),

                TernaryFilter::make('pay')
                    ->translateLabel()
                    ->nullable()
                    ->placeholder(__('All'))
                    ->trueLabel(__('Paid'))
                    ->falseLabel(__('Not Pay'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('pay', 1),
                        false: fn (Builder $query) => $query->where('pay', 0),
                        blank: fn (Builder $query) => $query,
                    ),

                TernaryFilter::make('poster')
                    ->translateLabel()
                    ->nullable()
                    ->native(false)
                    ->placeholder(__('All'))
                    ->trueLabel(__('In Poster'))
                    ->falseLabel(__('Not in Poster'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('poster', 1),
                        false: fn (Builder $query) => $query->where('poster', 0),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->slideOver(),
                    EditAction::make()->slideOver(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                CheckAsPaidAction::make('check'),
                CheckPosterAction::make('poster'),
                DeleteBulkAction::make(),
            ]);
    }
}
