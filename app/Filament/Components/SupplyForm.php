<?php

namespace App\Filament\Components;

use Auth;
use App\Models\{Supply, Supplier, Bill, Ingredient};

use Filament\Facades\Filament;

use Filament\Forms\{Get, Set};
use Filament\Support\RawJs;
use Filament\Forms\Components\Actions\Action as FormAction;

use Filament\Forms\Components\{Select, TextInput, Hidden, DateTimePicker, KeyValue, Section, SpatieMediaLibraryFileUpload, Tabs, Textarea, Toggle, ToggleButtons};
use Filament\Support\Enums\ActionSize;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;

class SupplyForm
{
    public static function schema(): array
    {
        return [
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
            ToggleButtons::make('bill_id')->label(__('Type pay'))
                ->options(Bill::get()->pluck('name', 'id'))
                ->default(0)
                ->required()
                ->inline()
                ->grouped()
                ->reactive(),
            ToggleButtons::make('pay')->label(__('Pay'))
                ->options(
                    [
                        0 => __('No pay'),
                        1 => __('Paid'),
                    ]
                )
                ->colors([
                    0 => 'danger',
                    2 => 'success',
                ])
                ->default(0)
                ->inline()
                ->grouped()
                ->reactive(),

            TextInput::make('sum')->label(__('Sum'))
                ->numeric()
                ->prefix('₴')
                ->autocomplete(false)
                ->inputMode('decimal'),
            TextInput::make('invoice_number')->label(__('Invoice'))
                ->visible(fn(Get $get) => $get('bill_id') == '1'),

            TextInput::make('delay')->label(__('Delay'))
            ->inputMode('none')
            ->suffixAction(
                FormAction::make('DelayPlus')
                    ->size(ActionSize::Large)
                    ->color('success')
                    ->icon('heroicon-o-plus')
                    ->iconButton()
                    ->disabled(fn (Get $get): bool => empty($get('delay')) || $get('delay') >= 28)
                    ->action(function (Get $get, Set $set, $state): void {
                        $set('delay', $get('delay') + 1);
                    })
            )
            ->prefixAction(
                FormAction::make('DelayMinus')
                    ->size(ActionSize::Large)
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
                    ->reactive()
                    ->numeric() // If you want to enforce numeric input
                    ->required(fn (callable $get) => $get('pay') === 1)
                    ->visible( fn(Get $get) => $get('pay') == '0'  ),

            Tabs::make('Tabs')
                ->contained(false)
                ->tabs([
                    Tabs\Tab::make(__('Comment'))
                    ->schema([

                        Textarea::make('comment')->label(__('Comment'))
                        ->rows(3),

                    ]),
                    Tabs\Tab::make(__('Photo'))
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('invoice')
                                ->label(__('Photo'))
                                ->collection('supplies')
                                ->multiple()
                                ->imagePreviewHeight(450)
                                ->imageResizeMode('cover')
                                ->optimize('webp')
                                ->openable()
                                ->resize(75),
                            ]),
                    Tabs\Tab::make(__('Receipt'))
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('receipt')
                                ->collection('receipts')
                                ->label(__('Receipt'))
                                ->multiple()
                                ->imagePreviewHeight(450)
                                ->imageResizeMode('cover')
                                ->optimize('webp')
                                ->resize(75)
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    null,
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->openable()


                            // ])->columns(1),
                            // Tabs\Tab::make(__('List'))
                            //     ->schema([
                            //         TableRepeater::make('items')
                            //             ->schema([
                            //                 Select::make('ingredient_id')->label(__('Ingredient'))
                            //                     ->searchable()
                            //                     ->preload()
                            //                     ->required()
                            //                     ->options(Ingredient::where('place_id', Filament::getTenant()->id)->pluck('ingredient', 'id'))
                            //                     ->reactive(),
                            //                 TextInput::make('qty')->translateLabel()
                            //                     ->reactive()
                            //                     ->suffix(static function (Get $get, $state) {
                            //                         $ingredient = Ingredient::find($get('ingredient_id'));
                            //                         return $ingredient;
                            //                     }),
                            //                 TextInput::make('price')->translateLabel()->suffix('')
                            //                     ->numeric()
                            //                     ->autocomplete(false)
                            //                     ->inputMode('decimal')
                            //                     ->stripCharacters(',')
                            //                     ->required()
                            //                     ->prefix('₴'),

                            //             ])

                        ]),


                ]),
        ];
    }
}
