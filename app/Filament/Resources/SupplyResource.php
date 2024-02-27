<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplyResource\Pages;
use App\Models\Bill;
use App\Models\Ingredient;
use App\Models\Supplier;
use App\Models\Supply;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Filament\Facades\Filament;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Njxqlus\Filament\Components\Infolists\LightboxSpatieMediaLibraryImageEntry;

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('supplier', 'user');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(Auth::user()->id),
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                DateTimePicker::make('added_at')
                    ->label(__('Date Added'))
                    ->native(false)
                    ->seconds(false)
                    ->maxDate(now())->default(now()),
                Select::make('supplier_id')->label(__('Supplier'))
                    ->relationship('supplier', 'company')
                    ->searchable()
                    ->native(false)
                    ->preload()
                    ->required()
                    ->options(Supplier::where('place_id', Filament::getTenant()->id)->visible()->pluck('company', 'id'))
                    ->createOptionForm([
                        Section::make()
                            ->schema([
                                Hidden::make('place_id')->default(Filament::getTenant()->id),
                                TextInput::make('name')->label(__('Name')),
                                TextInput::make('phone')->label(__('Phone')),
                                TextInput::make('company')->label(__('Company')),
                                TextInput::make('email')->label(__('Email'))->email(),
                                KeyValue::make('data')
                                    ->keyLabel(__('Rekvizites'))
                                    ->valueLabel(__('Value')),
                            ])->columns(2),
                    ]),
                TextInput::make('sum')->label(__('Sum'))
                    ->numeric()
                    ->required()
                    ->prefix('₴')
                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                Textarea::make('comment')->label(__('Comment'))
                    ->rows(3),
                Select::make('bill_id')->label(__('Type pay'))
                    ->default(0)
                    ->native(false)
                    ->options(Bill::get()->pluck('name', 'id'))
                    ->required(),
                Toggle::make('pay')->label(__('Paid'))
                    ->default(false)
                    ->inline(),

                Section::make(__('Photo'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('invoice')
                            ->label(__('Invoice'))
                            ->collection('supplies')
                            ->multiple()
                            ->imagePreviewHeight(450)
                            ->imageResizeMode('cover')
                            ->imageEditor()
                            ->optimize('webp')
                            ->resize(70)
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->reorderable()
                            ->openable(),

                        SpatieMediaLibraryFileUpload::make('receipt')
                            ->collection('receipts')
                            ->label(__('Receipt'))
                            ->multiple()
                            ->imagePreviewHeight(350)
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('2100')
                            ->imageEditor()
                            ->optimize('webp')
                            ->resize(90)
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->reorderable()
                            ->openable()
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

            //     Section::make(__('List'))
            //         ->schema([
            //             Repeater::make('supplyItems')->relationship()
            //                 ->schema([
            //                     Select::make('ingredient_id')
            //                         ->native(false)
            //                         ->searchable()
            //                         ->options(Ingredient::where('place_id', Filament::getTenant()->id)->pluck('ingredient', 'id'))
            //                         ->required(),
            //                     TextInput::make('qty')->translateLabel(),
            //                     TextInput::make('sum')->translateLabel()
            //                         ->prefix('₴')
            //                         ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
            //                 ])
            //                 ->columns(3)
            //                 ->collapsed()

            //         ])
            //
            ])

            ->columns(['lg' => 2, 'md' => 2, 'xs' => 1]);
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
                    ->summarize(Sum::make()->money('uah')->label(__('Total'))),

                SpatieMediaLibraryImageColumn::make('invoice')->label(__('Invoice'))
                    ->collection('supplies')
                    ->simpleLightbox()
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->height(30)
                    ->toggleable()
                    ->defaultImageUrl(url('/assets/imgs/app/no-photo.png'))
                    ->placeholder('No description.')
                    ->visibleFrom('md'),

                TextColumn::make('bill.type')->label(__('Bill'))->badge(),
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
                            $name => !$record->$name,
                        ]);
                    })
                    ->visibleFrom('md'),

            ])->defaultSort('added_at', 'desc')
            ->deferLoading()
            ->defaultPaginationPageOption(25)
            ->striped()
            ->filters([
                SelectFilter::make('supplier_id')
                    ->label(__('Supplier'))
                    ->native(false)
                    ->options(
                        Supplier::where('place_id', Filament::getTenant()->id)->visible(1)->pluck('company', 'id')
                    ),
                SelectFilter::make('user')->translateLabel()
                    ->options(
                        User::whereHas('place', function ($query) {
                            $query->where('place_id', Filament::getTenant()->id);
                        })->get()
                            ->pluck('name', 'id')
                    )
                    ->attribute('user_id'),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->translateLabel()->native(false)
                            ->placeholder(fn ($state): string => now()->subMonth()->format('d/m/Y')),
                        DatePicker::make('created_until')->translateLabel()->native(false)
                            ->placeholder(fn ($state): string => now()->format('d/m/Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('added_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('added_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Поставки с ' . Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Поставки до ' . Carbon::parse($data['created_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),

                SelectFilter::make('bill')->translateLabel()
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
                    ->placeholder(__('All'))
                    ->trueLabel(__('In Poster'))
                    ->falseLabel(__('Not in Poster'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('poster', 1),
                        false: fn (Builder $query) => $query->where('poster', 0),
                        blank: fn (Builder $query) => $query, // In this example, we do not want to filter the query when it is blank.
                    )
            ])

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->slideOver(),
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                // BulkAction::make('pay')->icon('heroicon-s-inbox-in')->translateLabel()
                // ->action(fn (Collection $records) => $records->each->pay())
                // ->deselectRecordsAfterCompletion(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make()->schema([
                    TextEntry::make('added_at')->label(__('Date Added'))->dateTime('d F Y')
                        ->badge(),
                    TextEntry::make('user.name')->translateLabel(),
                    TextEntry::make('supplier.company')->label(__('Supplier'))->badge(),
                    TextEntry::make('sum')->label(__('Sum'))->badge()->money('UAH', true),
                    TextEntry::make('bill.name')->translateLabel()
                        ->badge(),
                    TextEntry::make('comment')->label(__('Comment'))->badge(),
                ])->columns(2)->collapsible(),
                ComponentsSection::make(__('Invoice'))->schema([
                    LightboxSpatieMediaLibraryImageEntry::make('invoice')
                        ->collection('supplies')
                        ->conversion('thumb')
                        ->translateLabel(),
                    KeyValueEntry::make('supplier.data')->label(__('Rekvizites'))
                        ->keyLabel(' ')
                        ->valueLabel('Реквизиты')
                ])->columns(2)->collapsed(),

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
        ];
    }
}
