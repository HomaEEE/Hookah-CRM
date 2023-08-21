<?php

namespace App\Filament\Resources;

use App\Enums\Bill as BillEnum;
use App\Filament\Resources\SupplyResource\Pages;
use App\Models\Bill;
use App\Models\Supplier;
use App\Models\Supply;
use Auth;
use Filament\Forms\Get;
use Filament\Forms\Set;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SupplyResource extends Resource
{
    protected static ?string $model = Supply::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    public static function getNavigationGroup(): ?string
    {
        return __('Manage');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Supply');
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
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Hidden::make('user_id')->default(Auth::user()->id),
                                DateTimePicker::make('added_at')
                                ->label(__('Date Added'))
                                ->native(false)
                                ->maxDate(now())->default(now()),
                                Select::make('place_id')->label(__('Place'))
                                    ->options(
                                        Auth::user()->place->pluck('nameCity', 'id')
                                    )
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set) => $set('contact_id', null)),
                                Select::make('supplier_id')->label(__('Supplier'))
                                    ->relationship('supplier', 'company')
                                    ->hidden(fn (Get $get) => $get('place_id') == null)
                                    ->searchable()
                                    ->native(false)
                                    ->preload()
                                    ->required()
                                    ->options(function (callable $get) {
                                        if (null == ($get('place_id'))) {
                                            return null;
                                        } else {
                                            return Supplier::where('place_id', $get('place_id'))->visible()->pluck('company', 'id');
                                        }
                                    })
                                    ->createOptionForm([
                                        Section::make()
                                            ->schema([
                                                Select::make('place_id')->label(__('Place'))
                                                ->options(
                                                    Auth::user()->place->pluck('nameCity', 'id')
                                                )
                                                ->native(false)
                                                ->required(),
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
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                                Textarea::make('comment')->label(__('Comment'))
                                    ->rows(3),
                                Select::make('type_pay')->label(__('Type pay'))
                                    ->default(0)
                                    ->options([
                                        0 => __('Cash'),
                                        1 => __('Card')
                                    ])
                                    ->required(),
                                Toggle::make('pay')->label(__('Paid'))
                                    ->default(false)
                                    ->inline(),
                            ]),
                    ])->columns(2),
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('invoice')
                                    ->collection('supplies')
                                    ->multiple()
                                    ->maxFiles(10)
                                    ->imagePreviewHeight('250')
                                    ->openable(),
                            ])

                            ->columnSpan(['lg' => 2, 'md' => 2, 'xs' => 1]),
                    ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
                    ->size('sm')
                    ->description(fn (Supply $record): string => $record->user->name ?? ''),
                TextColumn::make('supplier.company')->label(__('Supplier'))
                ->description(fn (Supply $record): string => $record->place->city ?? ''),

                TextColumn::make('sum')->label(__('Sum'))->money('uah', true),
                // TextColumn::make('media')->collecction('supplies')->html()->label(__('Link')),
                TextColumn::make('type_pay')->label(__('Type pay'))
                ->badge()
                ->formatStateUsing(
                    fn (string $state): string => match ($state) {
                        '0' => __('Cash'),
                        '1' => __('Card'),
                    })
                    ->colors([
                        0 => 'info',
                        1 => 'danger',
                    ]),
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
            ->filters([
                SelectFilter::make('Place')
                    ->label(__('Place'))
                // build the filter form
                    ->form([
                        Select::make('place_id')->label(__('Place'))
                            ->searchable()
                            ->options(
                                Auth::user()->place->pluck('nameCity', 'id')
                            )
                            ->afterStateUpdated(
                                function (callable $set) {
                                    $set('supplier_id', null);
                                }
                            ),
                        Select::make('supplier_id')->label(__('Supplier'))
                            ->searchable()
                            ->hidden(fn (Get $get) => $get('place_id') == null)
                            ->preload()
                            ->options(function (callable $get) {
                                if (null == ($get('place_id'))) {
                                    return null;
                                } else {
                                    return Supplier::where('place_id', $get('place_id'))->pluck('company', 'id');
                                }
                            }),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $place_id = (int) $data['place_id'];
                        $supplier_id = (int) $data['supplier_id'];

                        if (! empty($supplier_id)) {
                            $query->where('supplier_id', '=', $supplier_id);

                        }

                        if (! empty($place_id)) {
                            $query->where('place_id', '=', $place_id);
                        }
                    }
                    ),

                SelectFilter::make('type_pay')->translateLabel()
                    ->options(
                        [
                            0 => __('Cash'),
                            1 => __('Card')
                        ]
                    )
                    ->attribute('type_pay'),

                // Filter::make('created_at')
                //     ->form([
                //         Forms\Components\DatePicker::make('added_at')->label(__('Added at'))
                //             ->nullable(),
                //     ])
                //     ->query(function (Builder $query, array $data): Builder {
                //         return $query
                //             ->when(
                //                 $data['added_at'],
                //                 fn (Builder $query, $date): Builder => $query->whereDate('created_at', '=', $date),
                //             );
                //     }),

                TernaryFilter::make('pay')->label(__('Pay'))
                    ->placeholder((__('All')))
                    ->trueLabel(__('Paid'))
                    ->falseLabel(__('Not Pay'))
                    ->queries(
                        true: fn (Builder $query) => $query->Paid(),
                        false: fn (Builder $query) => $query->NotPaid(),
                        blank: fn (Builder $query) => $query->AllPaid(),
                    ),
                TernaryFilter::make('poster')->translateLabel()
                    ->placeholder((__('All')))
                    ->trueLabel(__('In Poster'))
                    ->falseLabel(__('Not in Poster'))
                    ->queries(
                        true: fn (Builder $query) => $query->InPoster(),
                        false: fn (Builder $query) => $query->NotPoster(),
                        blank: fn (Builder $query) => $query->AllPoster(),
                    ),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSupplies::route('/'),
        ];
    }
}
