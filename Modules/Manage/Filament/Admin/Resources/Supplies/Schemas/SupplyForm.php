<?php

namespace Modules\Manage\Filament\Admin\Resources\Supplies\Schemas;

use App\Enums\IngredientUnit;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\Auth;
use Modules\Finance\Enums\BillType;
use Modules\Manage\Filament\Actions\OCRAction;
use Modules\Manage\Models\Ingredient;
use Modules\Manage\Models\IngredientAlias;
use Modules\Place\Models\Reqvizite;
use Modules\Place\Models\Supplier;

class SupplyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::user()->id),
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                DateTimePicker::make('added_at')
                    ->label(__('Date Added'))
                    ->native(false)
                    ->seconds(false)
                    ->maxDate(now())->default(now()),

                ToggleButtons::make('bill_id')->label(__('Type pay'))
                    ->options(BillType::class)
                    ->default(1)
                    ->required()
                    ->inline()
                    ->grouped()
                    ->extraAttributes(['style' => 'width: 100%'])
                    ->colors([
                        0 => 'info',
                        1 => 'success',
                    ])
                    ->live(),

                ToggleButtons::make('pay')->label(__('Pay'))
                    ->options(
                        [
                            0 => __('No pay'),
                            1 => __('Paid'),
                        ]
                    )
                    ->colors([
                        0 => 'danger',
                        1 => 'success',
                    ])
                    ->default(0)
                    ->inline()
                    ->live()
                    ->grouped()
                    ->extraAttributes(['style' => 'width: 100%'])
                    ->live(),

                Select::make('supplier_id')->label(__('Supplier'))
                    ->relationship('supplier', 'company')
                    ->searchable()
                    ->searchingMessage('Searching supplier...')
                    ->searchDebounce(500)
                    ->native(false)
                    ->required()
                    ->options(Supplier::where('place_id', Filament::getTenant()->id)->visible()->pluck('company', 'id'))
                    ->createOptionForm([
                        Section::make()
                            ->schema([
                                TextInput::make('name')->label(__('Name')),
                                TextInput::make('phone')->label(__('Phone')),
                                TextInput::make('company')->label(__('Company')),
                                TextInput::make('mail')->label(__('Email'))->email(),
                            ])->columns(2),
                    ]),

                Select::make('reqvizite_id')
                    ->label('Реквизиты')
                    ->preload()
                    ->live()
                    ->options(function (callable $get) {
                        if (null == ($get('supplier_id'))) {
                            return null;
                        } else {
                            return Reqvizite::where('supplier_id', $get('supplier_id'))->pluck('own', 'id');
                        }
                    })

                    ->createOptionForm(fn ($get) => [
                        Section::make()
                            ->schema([
                                Hidden::make('supplier_id')
                                    ->default(fn () => $get('supplier_id')),
                                TextInput::make('IBAN')->label(__('IBAN'))->prefix('UA')->numeric(),
                                TextInput::make('INN')->label(__('INN'))->numeric(),
                                TextInput::make('own')->label(__('FOP')),
                            ])->columns(1),
                    ])
                    ->createOptionUsing(function (array $data) {
                        return Reqvizite::create([
                            'supplier_id' => $data['supplier_id'],
                            'IBAN' => $data['IBAN'],
                            'INN' => $data['INN'],
                            'own' => $data['own'],
                        ])->id;
                    })
                    ->searchable()
                    ->live()
                    ->disabled(fn (Get $get) => $get('supplier_id') === null)
                    ->required(fn (Get $get) => $get('bill_id') === BillType::CARD && (int) $get('pay') === 0)
                    ->visible(fn (Get $get) => (int) $get('pay') === 0 && $get('bill_id') === BillType::CARD),

                TextInput::make('invoice_number')->label(__('Invoice'))
                    ->visible(fn (Get $get) => $get('bill_id') === BillType::CARD)
                    ->required(fn (Get $get) => $get('bill_id') === BillType::CARD->value && (int) $get('pay') === 0),

                TextInput::make('sum')->label(__('Sum'))
                    ->numeric()
                    ->prefix('₴')
                    ->inputMode('decimal')
                    ->required(),

                TextInput::make('delay')->label(__('Delay'))
                    ->inputMode('none')
                    ->suffixAction(
                        Action::make('DelayPlus')
                            ->size(Size::Large)
                            ->color('success')
                            ->icon('heroicon-o-plus')
                            ->iconButton()
                            ->disabled(fn (Get $get): bool => empty($get('delay')) || $get('delay') >= 28)
                            ->action(function (Get $get, Set $set, $state): void {
                                $set('delay', $get('delay') + 1);
                            })
                    )
                    ->prefixAction(
                        Action::make('DelayMinus')
                            ->size(Size::Large)
                            ->icon('heroicon-o-minus')
                            ->color('danger')
                            ->iconButton()
                            ->disabled(fn (Get $get): bool => empty($get('delay')) || $get('delay') <= 1)
                            ->action(function (Get $get, Set $set, $state): void {
                                $set('delay', $get('delay') - 1);
                            })
                    )
                    ->default(
                        function (callable $get, $set) {
                            $toggle = $get('pay');

                            return $toggle === 0 ? $set('delay', 7) : $set('delay', 0); // Or '' for an empty string
                        })
                    ->live()
                    ->numeric() // If you want to enforce numeric input
                    ->required(fn (callable $get) => $get('pay') === 1)
                    ->visible(fn (Get $get) => $get('pay') == '0'),

                Tabs::make('Tabs')
                    ->contained(false)
                    ->tabs([

                        Tab::make(__('Photo'))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('invoice')
                                    ->label(__('Photo'))
                                    ->collection('supplies')
                                    ->multiple()
                                    ->automaticallyResizeImagesMode('cover')
                                    ->imagePreviewHeight(350)
                                    ->customProperties(['format' => 'webp', 'width' => 1800, 'quality' => 50])
                                    ->openable()
                                    ->preserveFilenames()
                                    ->live()
                                    ->hintAction(OCRAction::make()),

                                SpatieMediaLibraryFileUpload::make('receipt')
                                    ->collection('receipts')
                                    ->label(__('Receipt'))
                                    ->multiple()
                                    ->automaticallyResizeImagesMode('cover')
                                    ->imagePreviewHeight(350)
                                    ->customProperties(['format' => 'webp', 'width' => 1800, 'quality' => 50])
                                    ->openable()
                                    ->preserveFilenames(),

                                Textarea::make('comment')->label(__('Comment'))
                                    ->rows(3),
                            ]),

                        // Tab::make(__('List'))
                        //     ->columns(2)
                        //     ->schema([
                        //         Repeater::make('items')
                        //             ->columnSpanFull()
                        //             ->schema([
                        //                 TextInput::make('name')
                        //                     ->label(__('Invoice name'))
                        //                     ->disabled()
                        //                     ->dehydrated()
                        //                     ->visible(fn (Get $get) => empty($get('ingredient_id'))),

                        //                 Select::make('ingredient_id')
                        //                     ->label(__('Ingredient'))
                        //                     ->searchable()
                        //                     ->getSearchResultsUsing(fn (string $search): array => Ingredient::where('ingredient', 'like', "%{$search}%")->limit(10)->pluck('ingredient', 'id')->toArray())
                        //                     ->getOptionLabelUsing(fn ($value): ?string => Ingredient::find($value)?->ingredient)
                        //                     ->preload()
                        //                     // ->required(fn (Get $get) => empty($get('ingredient_id')))
                        //                     ->reactive()
                        //                     ->afterStateUpdated(function ($state, callable $get, $set) {
                        //                         $name = $get('name');
                        //                         if (! empty($name) && ! empty($state)) {
                        //                             // Create alias if it doesn't exist
                        //                             IngredientAlias::firstOrCreate(
                        //                                 ['alias' => $name],
                        //                                 ['ingredient_id' => $state]
                        //                             );
                        //                         }

                        //                         $set('unit', $state ? Ingredient::find($state)->unit : IngredientUnit::kg);
                        //                     }),
                        //                 FusedGroup::make([
                        //                     TextInput::make('qty')
                        //                         ->label(__('qty'))
                        //                         ->translateLabel()
                        //                         ->numeric()
                        //                         ->required()
                        //                         ->live(onBlur: true)
                        //                         ->afterStateUpdated(function (Set $set, Get $get) {
                        //                             $sum = (float) $get('qty') * (float) $get('price');
                        //                             $set('sum', round($sum, 2));
                        //                         })
                        //                         ->columnSpan(2),

                        //                     Select::make('unit')
                        //                         ->translateLabel()
                        //                         ->live()
                        //                         ->options(IngredientUnit::class),
                        //                     // ->default(IngredientUnit::kg),
                        //                 ])->columns(3),
                        //                 FusedGroup::make([
                        //                     TextInput::make('price')
                        //                         ->translateLabel()
                        //                         ->numeric()
                        //                         ->inputMode('decimal')
                        //                         ->live(onBlur: true)
                        //                         ->afterStateUpdated(function (Set $set, Get $get) {
                        //                             $sum = (float) $get('qty') * (float) $get('price');
                        //                             $set('sum', round($sum, 2));
                        //                         })
                        //                         ->prefix('₴'),

                        //                     TextInput::make('sum')
                        //                         ->translateLabel()
                        //                         ->numeric()
                        //                         ->inputMode('decimal')
                        //                         ->live(onBlur: true)
                        //                         ->afterStateUpdated(function (Set $set, Get $get) {
                        //                             $price = (float) $get('sum') / (float) $get('qty');
                        //                             $set('price', round($price, 2));
                        //                         })
                        //                         ->prefix('₴'),
                        //                 ])->columns(2),
                        //             ]),
                        //         Toggle::make('VAT')->label(__('VAT'))->live(onBlur: true)->inline()
                        //             ->onIcon('heroicon-m-calculator')
                        //             ->offIcon('heroicon-m-x-mark'),
                        //         TextEntry::make('invoice_sum')->hiddenLabel()->prefix('₴')

                        //             ->state(function (Set $set, Get $get) {
                        //                 if (! empty($get('VAT'))) {
                        //                     $totalInvoiceSum = collect($get('items'))
                        //                         ->pluck('sum')
                        //                         ->sum();
                        //                     $totalInvoiceSum = $totalInvoiceSum * 1.20;
                        //                 } else {
                        //                     $totalInvoiceSum = collect($get('items'))
                        //                         ->pluck('sum')
                        //                         ->sum();
                        //                 }

                        //                 return $totalInvoiceSum;
                        //                 $set('sum', $totalInvoiceSum);
                        //                 $set('invoice_sum', $totalInvoiceSum);

                        //                 return $totalInvoiceSum;
                        //             })
                        //             ->live(),
                        //     ]),

                    ]),

            ]);
    }
}
