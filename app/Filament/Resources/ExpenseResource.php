<?php

namespace App\Filament\Resources;

use App\Filament\Components\ExpenseForm;
use App\Filament\Components\Filters\LastMonthRecords;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Http\Controllers\PosterApiController as PosterApi;
use App\Models\{Bill, Expense, ExpenseCategory};
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\{IconColumn, Summarizers\Sum};
use Filament\Tables\Filters\{Filter, SelectFilter, TernaryFilter};
use Illuminate\Database\Eloquent\Builder;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Njxqlus\Filament\Components\Infolists\LightboxSpatieMediaLibraryImageEntry;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $recordTitleAttribute = 'id';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
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

                IconColumn::make('bill_id')->label(__('Bill'))
                ->icon(fn (string $state): string => match ($state) {
                    '0' => 'heroicon-o-banknotes',
                    '1' => 'heroicon-o-credit-card',
                })
                ->color(fn (string $state): string => match ($state) {
                    '1' => 'warning',
                    '0' => 'success',
                    default => 'gray',
                })
                ->action(function ($record, $column) {
                    $name = $column->getName();
                    $record->update([
                        $name => ! $record->$name,
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
                            $name => !$record->$name,
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
                            $name => !$record->$name,
                        ]);
                    }),

            ])->defaultSort('added_at', 'desc')
            ->deferLoading()
            ->defaultPaginationPageOption(10)
            ->striped()
            ->filters([
                DateRangeFilter::make('created_at')->translateLabel()->separator(' - ')
                ->startDate(Carbon::now()->subMonth())->endDate(Carbon::now())
                ->modifyQueryUsing(fn(Builder $query, ?Carbon $startDate , ?Carbon $endDate , $dateString) =>
                    $query->when(!empty($dateString),
                        fn (Builder $query) : Builder =>
                            $query->whereBetween('created_at', [$startDate,$endDate]))
            ),
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
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('added_at')->translateLabel()->badge()->dateTime('d F Y'),
                TextEntry::make('user.name')->translateLabel()->badge(),
                TextEntry::make('bill.name')->translateLabel()->badge(),
                TextEntry::make('category.name')->translateLabel()->badge(),
                TextEntry::make('sum')->translateLabel()->badge(),
                TextEntry::make('comment')->translateLabel()->badge(),
                LightboxSpatieMediaLibraryImageEntry::make('invoice')
                    ->collection('supplies')
                    ->conversion('thumb')
                    ->translateLabel(),
                // ImageEntry::make('invoice')
                // ->simpleLightbox(fn ($record) =>  $record?->image ?? "Your Image Url address", defaultDisplayUrl: true)
                // ->translateLabel(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageExpenses::route('/'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
           // App\Filament\Widgets\ExpenseChart::class
        ];
    }

    public function updateCategories(array $data): void
    {
        if (blank($data['place_id'])) {
            $this->notify('danger', __('Please, select place'));
        } else {
            (new PosterApi())->getTransatcionCategories($data['place_id']);
            $this->notify('success', __('Menu updated'));
        }
    }
}
