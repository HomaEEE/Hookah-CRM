<?php

namespace App\Filament\Resources;

use App\Filament\Components\Bulks\CheckAsPaidAction;
use App\Filament\Components\Bulks\CheckPosterAction;
use App\Filament\Components\SupplyForm;
use App\Filament\Resources\SupplyResource\Pages;
use App\Models\Bill;
use App\Models\Ingredient;
use App\Models\Supplier;
use App\Models\Supply;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class SupplyResource extends Resource
{
    protected static ?string $model = Supply::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Manage');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Supplies');
    }

    public static function getNavigationLabel(): string
    {
        return __('Supplies');
    }

    protected function getTableQuery(): Builder
    {
        return Supply::query()->with(['supplier', 'user', 'bill'])->fastPaginate();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                SupplyForm::schema(),
            );
    }

    public static function table(Table $table): Table
    {
        return $table
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
                    ->simpleLightbox()
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->height(40)
                    ->defaultImageUrl(url('/assets/imgs/app/no-photo.png'))
                    ->placeholder('No description.'),

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
           // ->deferLoading()
            ->defaultPaginationPageOption(25)
            ->striped()
            ->filters([

                DateRangeFilter::make('created_at')->translateLabel()->separator(' - ')
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
                        //  modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant())
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
                        blank: fn (Builder $query) => $query, // In this example, we do not want to filter the query when it is blank.
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
                        blank: fn (Builder $query) => $query, // In this example, we do not want to filter the query when it is blank.
                    ),
            ])

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->mutateRecordDataUsing(function (array $data): array {
                            $data['sum'] = $data['sum'] / 100;

                            return $data;
                        })
                        ->mutateFormDataUsing(function (array $data): array {
                            $data['sum'] = $data['sum'] * 100;

                            return $data;
                        }),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                // BulkAction::make('pay')->icon('heroicon-s-inbox-in')->translateLabel()
                // ->action(fn (Collection $records) => $records->each->pay())
                // ->deselectRecordsAfterCompletion(),
                CheckAsPaidAction::make('check'),
                CheckPosterAction::make('poster'),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('added_at')->label(__('Date Added'))->dateTime('d F Y')
                    ->badge(),
                TextEntry::make('user.name')->translateLabel(),
                TextEntry::make('supplier.company')->label(__('Supplier'))->badge(),
                TextEntry::make('sum')->label(__('Sum'))->badge()->money('UAH')->copyable(),
                TextEntry::make('bill.name')->translateLabel()
                    ->badge(),
                TextEntry::make('invoice_number')->translateLabel()->badge()->copyable(),
                KeyValueEntry::make('supplier.data')->label(__('Rekvizites'))
                    ->keyLabel(' ')
                    ->valueLabel('Реквизиты'),

                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make(__('Comment'))
                            ->schema([
                                TextEntry::make('comment')->label(__('Comment'))->badge(),
                            ]),
                        Tabs\Tab::make(__('Photo'))
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('invoice')
                                    ->collection('supplies')
                                    ->simpleLightbox()
                                    ->hiddenLabel(),
                            ]),
                        Tabs\Tab::make(__('Supply'))
                            ->schema([
                                // SpatieMediaLibraryImageEntry::make('invoice')
                                // ->collection('supplies')
                                // ->simpleLightbox()
                                // ->hiddenLabel(),
                            ]),
                    ])->columnSpanFull()
                    ->contained(false),

                // ComponentsSection::make(__('List'))->schema([
                //     RepeatableEntry::make('supplyItems')->schema([
                //         TextEntry::make('ingredient.ingredient'),
                //         TextEntry::make('qty')->translateLabel(),
                //         TextEntry::make('sum')->translateLabel()->badge(),
                //     ])->columns(3)
                //     ->contained(false)

                // ])->columns(1)->collapsed(),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSupplies::route('/'),
            // 'view' => Pages\ViewSupply::route('/{record}/view'),
            // 'edit' => Pages\EditSupply::route('/{record}/edit'),
        ];
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('items')
            ->relationship()
            ->schema([
                Select::make('ingredient_id')
                    ->label('Product')->translateLabel()
                    ->relationship('ingredient', 'id')
                    ->options(Ingredient::where('place_id', Filament::getTenant()->id)->get()->pluck('ingredient', 'id'))
                    ->searchable(['ingredient', 'barcode'])
                    ->required()
                    ->native(false)
                    ->reactive()
                    ->afterStateUpdated(fn ($state, Set $set) => $set('unit_price', Ingredient::find($state)?->price / 100 ?? 0))
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->columnSpan([
                        'md' => 6,
                    ])
                    ->searchable(),

                TextInput::make('qty')
                    ->translateLabel()
                    ->numeric()
                    ->default(1)
                    ->columnSpan([
                        'md' => 2,
                    ])
                    ->afterStateUpdated(fn (Set $set, Get $get) => $set('sum', $get('qty') * $get('unit_price')))
                    ->reactive()
                    ->required(),

                TextInput::make('unit_price')
                    ->label('Unit Price')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->required()
                    ->columnSpan([
                        'md' => 2,
                    ]),

                TextInput::make('sum')
                    ->label('Sum')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->required()
                    ->columnSpan([
                        'md' => 2,
                    ]),
            ])
            // ->extraItemActions([
            //     Action::make('openProduct')
            //         ->tooltip('Open product')
            //         ->icon('heroicon-m-arrow-top-right-on-square')
            //         ->url(function (array $arguments, Repeater $component): ?string {
            //             $itemData = $component->getRawItemState($arguments['item']);

            //             $ingredient = Ingredient::find($itemData['ingredient_id']);

            //             if (! $ingredient) {
            //                 return null;
            //             }

            //             return IngredientResource::getUrl('index', ['record' => $ingredient]);
            //         }, shouldOpenInNewTab: true)
            //         ->hidden(fn (array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['ingredient_id'])),
            // ])
            // ->orderColumn('ingredient')
            ->defaultItems(1)
            ->hiddenLabel()
            ->columns([
                'md' => 12,
            ])
            ->required();
    }
}
