<?php

namespace App\Filament\Components;

use App\Models\Bill;
use App\Models\Ingredient;
use App\Models\Reqvizite;
use App\Models\Supplier;
use Auth;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Size;

class SupplyForm
{
    public static function schema(): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
            //  Hidden::make('place_id')->default(Filament::getTenant()->id),
            DateTimePicker::make('added_at')
                ->label(__('Date Added'))
                ->native(false)
                ->seconds(false)
                ->maxDate(now())->default(now()),
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

            ToggleButtons::make('bill_id')->label(__('Type pay'))
                ->options(Bill::get()->pluck('name', 'id'))
                ->default(0)
                ->required()
                ->inline()
                ->grouped()
                ->extraAttributes(['style' => 'width: 100%'])
                ->colors([
                    0 => 'info',
                    1 => 'success',
                ])
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
                    1 => 'success',
                ])
                ->default(0)
                ->inline()
                ->live()
                ->grouped()
                ->extraAttributes(['style' => 'width: 100%'])
                ->reactive(),

            Select::make('reqvizite_id')
                ->label('Реквизиты')
                ->preload()
                ->reactive()
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
                ->reactive()
                ->disabled(fn (Get $get) => $get('supplier_id') === null)
                ->required(fn (Get $get) => (int) $get('bill_id') === 1 && (int) $get('pay') === 0)
                ->visible(fn (Get $get) => (int) $get('pay') === 0 && (int) $get('bill_id') === 1),

            TextInput::make('invoice_number')->label(__('Invoice'))
                ->visible(fn (Get $get) => (int) $get('bill_id') === 1 && (int) $get('pay') === 0)
                ->required(fn (Get $get) => (int) $get('bill_id') === 1 && (int) $get('pay') === 0),

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
                ->reactive()
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
                                ->imageResizeMode('cover')
                                ->imagePreviewHeight(350)
                                ->customProperties(['format' => 'webp', 'width' => 1800, 'quality' => 50])
                                ->openable(),
                            Textarea::make('comment')->label(__('Comment'))
                                ->rows(3),
                        ]),

                    Tab::make(__('Receipt'))
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('receipt')
                                ->collection('receipts')
                                ->label(__('Receipt'))
                                ->multiple()
                                ->imagePreviewHeight(450)
                                ->imageResizeMode('cover')
                                ->imagePreviewHeight(350)
                                ->imageResizeTargetWidth('1800')
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    null,
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->openable(),

                        ])->columns(1),
                    // Tabs\Tab::make(__('List'))
                    //     ->schema([
                    //         Repeater::make('items')
                    //             ->schema([
                    //                 TextInput::make('name')
                    //                     ->label(__('Имя из накладной'))
                    //                     ->disabled(),
                    //                 Select::make('ingredient_id')
                    //                     ->label(__('Ingredient'))
                    //                     ->searchable()
                    //                     ->preload()
                    //                     ->options(Ingredient::get()->pluck('ingredient', 'id'))
                    //                     ->required(fn (Get $get) => empty($get('ingredient_id')))
                    //                     ->visible(fn (Get $get) => empty($get('ingredient_id'))),
                    //                 TextInput::make('qty')->translateLabel()
                    //                     ->reactive(),
                    //                 TextInput::make('price')->translateLabel()->suffix('')
                    //                     ->numeric()
                    //                     ->autocomplete(false)
                    //                     ->inputMode('decimal')
                    //                     ->stripCharacters(',')
                    //                     ->required()
                    //                     ->prefix('₴'),

                    //             ]),
                    //     ]),

                ]),

        ];
    }
}
