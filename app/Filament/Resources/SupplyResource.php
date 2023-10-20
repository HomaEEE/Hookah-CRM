<?php

namespace App\Filament\Resources;

use App\Enums\Bill as BillEnum;
use App\Filament\Resources\SupplyResource\Pages;
use App\Models\Bill;
use App\Models\Supplier;
use App\Models\Supply;
use Auth;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Get;
use Filament\Forms\Set;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
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
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Njxqlus\Filament\Components\Infolists\LightboxSpatieMediaLibraryImageEntry;

class SupplyResource extends Resource
{
    protected static ?string $model = Supply::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $recordTitleAttribute = 'id';


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
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Hidden::make('user_id')->default(Auth::user()->id),
                                Hidden::make('place_id')->default(Filament::getTenant()->id),
                                DateTimePicker::make('added_at')
                                ->label(__('Date Added'))
                                ->native(false)
                                ->maxDate(now())->default(now()),
                                Select::make('supplier_id')->label(__('Supplier'))
                                    ->relationship('supplier', 'company')
                                    ->searchable()
                                    ->native(false)
                                    ->preload()
                                    ->required()
                                    ->options( Supplier::where('place_id', Filament::getTenant()->id)->visible()->pluck('company', 'id'))
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
                                    ->prefix('₴')
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
                                    ->imagePreviewHeight(350)
                                    ->imageEditor()
                                    ->optimize('webp')
                                    ->resize(50)
                                    ->imageEditorAspectRatios([
                                        null,
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->reorderable()
                                    ->openable()
                            ])

                            ->columnSpan(['lg' => 2, 'md' => 2, 'xs' => 1]),
                    ])->columns(2),

            ]);
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
                TextColumn::make('supplier.company')->label(__('Supplier'))->searchable(),
                TextColumn::make('sum')->label(__('Sum'))->money('uah', true)->searchable(),
                SpatieMediaLibraryImageColumn::make('invoice')->label(__('Invoice'))
                ->collection('supplies')
                ->circular()
                ->stacked()
                ->limit(3)
                ->limitedRemainingText()
                ->height(30)
                ->toggleable()
                ->defaultImageUrl(url('/assets/imgs/app/no-photo.png'))
                ->placeholder('No description.')
                ->visibleFrom('md'),
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
            ->deferLoading() //Ассинхронная загрузка
            ->filters([
                SelectFilter::make('Place')
                    ->label(__('Place'))
                // build the filter form
                    ->form([
                        Select::make('supplier_id')->label(__('Supplier'))
                            ->searchable()
                            ->preload()
                            ->options(
                                Supplier::where('place_id', Filament::getTenant()->id)->pluck('company', 'id')
                                ),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $place_id = (int) Filament::getTenant()->id;
                        $supplier_id = (int) $data['supplier_id'];

                        if (! empty($supplier_id)) {
                            $query->where('supplier_id', '=', $supplier_id);

                        }
                    }
                    ),

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

                SelectFilter::make('type_pay')->translateLabel()
                    ->options(
                        [
                            0 => __('Cash'),
                            1 => __('Card')
                        ]
                    )
                    ->attribute('type_pay'),


                    TernaryFilter::make('pay')
                    ->translateLabel()
                    ->nullable()
                    ->placeholder(__('All'))
                    ->trueLabel(__('Paid'))
                    ->falseLabel(__('Not Pay')),

                    TernaryFilter::make('poster')
                    ->translateLabel()
                    ->nullable()
                    ->placeholder(__('All'))
                    ->falseLabel(__('In Poster'))
                    ->trueLabel(__('Not in Poster')),
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
            TextEntry::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
            ->badge(),
            TextEntry::make('supplier.company')->label(__('Supplier')),
            TextEntry::make('sum')->label(__('Supplier')),
            TextEntry::make('type_pay')->translateLabel(),
            LightboxSpatieMediaLibraryImageEntry::make('invoice')->translateLabel()
            ->collection('supplies')
            ->conversion('thumb')
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSupplies::route('/'),
        ];
    }
}
