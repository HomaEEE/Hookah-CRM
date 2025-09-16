<?php

namespace App\Filament\Resources\Expenses;

use App\Filament\Components\ExpenseForm;
use App\Filament\Resources\Expenses\Pages\ManageExpenses;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $tentantOwnershipRelationName = 'owner';

    // protected static ?string $tentantRelationName = 'placeLabel';

    protected static ?int $navigationSort = 2;

    public static function getPluralModelLabel(): string
    {
        return __('Expenses');
    }

    public static function getNavigationLabel(): string
    {
        return __('Expenses');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('category', 'user', 'supply');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                ExpenseForm::schema()
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d-m-Y')
                    ->size('md')
                    ->badge()
                    ->description(fn (Expense $record): string => $record->user->name ?? ''),

                TextColumn::make('category.name')->label(__('Category'))
                    ->badge()
                    ->description(fn (Expense $record): string => $record->supply->supplier->company ?? ''),

                TextColumn::make('supply_id')->badge()->label(__('Supply'))
                    ->wrap(),
                TextColumn::make('sum')->label(__('Sum'))->money('UAH')
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total'))),

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
                    }),
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
                    }),

            ])->defaultSort('added_at', 'desc')
            ->deferLoading()
            ->defaultPaginationPageOption(10)
            ->striped()
            ->filters([
                DateRangeFilter::make('created_at')->translateLabel()
                    ->defaultLast30Days(),
                SelectFilter::make('category_id')
                    ->label(__('Category'))
                    ->native(false)
                    ->options(
                        ExpenseCategory::where('place_id', Filament::getTenant()->id)->pluck('name', 'id')
                    ),
                SelectFilter::make('bill')->translateLabel()
                    ->options(Bill::get()->pluck('name', 'id'))
                    ->native(false)
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
                        blank: fn (Builder $query) => $query
                    ),

                TernaryFilter::make('poster')
                    ->translateLabel()
                    ->nullable()
                    ->native(false)
                    ->placeholder(__('All'))
                    ->falseLabel(__('In Poster'))
                    ->trueLabel(__('Not in Poster'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('poster', 1),
                        false: fn (Builder $query) => $query->where('poster', 0),
                        blank: fn (Builder $query) => $query, // In this example, we do not want to filter the query when it is blank.
                    ),

                // TernaryFilter::make('pay')->label(__('Pay'))
                //     ->placeholder((__('All')))
                //     ->trueLabel(__('Paid'))
                //     ->falseLabel(__('Not Pay'))
                //     ->queries(
                //         true: fn (Builder $query) => $query->Paid(),
                //         false: fn (Builder $query) => $query->NotPaid(),
                //         blank: fn (Builder $query) => $query->AllPaid(),
                //     ),
                // TernaryFilter::make('poster')->translateLabel()
                //     ->placeholder((__('All')))
                //     ->trueLabel(__('In Poster'))
                //     ->falseLabel(__('Not in Poster'))
                //     ->queries(
                //         true: fn (Builder $query) => $query->InPoster(),
                //         false: fn (Builder $query) => $query->NotPoster(),
                //         blank: fn (Builder $query) => $query->AllPoster(),
                //     ),

            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('added_at')->translateLabel()->badge()->dateTime('d F Y'),
                TextEntry::make('user.name')->translateLabel()->badge(),
                TextEntry::make('bill.name')->translateLabel()->badge(),
                TextEntry::make('category.name')->translateLabel()->badge(),
                TextEntry::make('sum')->translateLabel()->badge(),
                TextEntry::make('comment')->translateLabel()->badge(),
                SpatieMediaLibraryImageEntry::make('invoice')
                    ->collection('supplies')
                    ->simpleLightbox()
                    ->hiddenLabel(),
                // ImageEntry::make('invoice')
                // ->simpleLightbox(fn ($record) =>  $record?->image ?? "Your Image Url address", defaultDisplayUrl: true)
                // ->translateLabel(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageExpenses::route('/'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // App\Filament\Widgets\ExpenseChart::class
        ];
    }
}
