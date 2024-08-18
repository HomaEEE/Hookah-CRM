<?php

namespace App\Filament\Components;

use Auth;
use App\Models\{Supply, Supplier, Bill};
use Filament\Facades\Filament;

use Filament\Forms\{Get, Set};
use Filament\Support\RawJs;

use Filament\Forms\Components\{Select, TextInput, Hidden, DateTimePicker, KeyValue, Section, SpatieMediaLibraryFileUpload, Tabs, Textarea, Toggle, ToggleButtons};

class SupplyForm
{
    public static function schema(array $options = []): array
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
                ToggleButtons::make('bill_id')->label(__('Type pay'))
                    ->options(Bill::get()->pluck('name', 'id'))
                    ->default(0)
                    ->required()
                    ->inline()
                    ->grouped()
                    ->live(),
                TextInput::make('sum')->label(__('Sum'))
                    ->numeric()
                    ->autocomplete(false)
                    ->inputMode('decimal')
                    ->stripCharacters(',')
                    ->required()
                    ->prefix('₴')
                    ->mask(RawJs::make('$money($input)')),
                TextInput::make('invoice_number')->label(__('Invoice'))
                ->visible(fn (Get $get) => $get('bill_id') == '1'),
                Textarea::make('comment')->label(__('Comment'))
                    ->rows(3),
                Toggle::make('pay')->label(__('Paid'))
                    ->default(false)
                    ->inline(),


                SpatieMediaLibraryFileUpload::make('invoice')
                    ->label(__('Invoice'))
                    ->collection('supplies')
                    ->multiple()
                    ->imagePreviewHeight(450)
                    ->imageResizeMode('contain')
                    ->imageEditor()
                    ->optimize('webp')
                    ->resize(80)
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->reorderable()
                    ->openable(),

                Section::make()
                    ->hidden($options['tabsHidden'] ?? false)
                    ->schema([
                        Tabs::make('Tabs')
                        ->contained(false)
                        ->tabs([
                            Tabs\Tab::make(__('Photo'))
                                ->schema([
                            SpatieMediaLibraryFileUpload::make('receipt')
                                ->collection('receipts')
                                ->label(__('Receipt'))
                                ->multiple()
                                ->imagePreviewHeight(250)
                                ->imageResizeTargetWidth('2400')
                                ->imageResizeTargetHeight('1600')
                                ->optimize('webp')
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    null,
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->reorderable()
                                ->openable()


                                ])->columns(1),
                            Tabs\Tab::make(__('List'))
                                ->schema([
                                    //static::getItemsRepeater()
                                ]),

                            ]),
                    ])
                    ->columns(1),
        ];
    }
}
