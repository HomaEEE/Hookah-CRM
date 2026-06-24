<?php

namespace Modules\Manage\Filament\Admin\Resources\Inventory\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')->schema([
                    TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255),

                    Select::make('warehouse_id')
                        ->label(__('Storage'))
                        ->relationship('warehouse', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Section::make()
                                ->schema([
                                    TextInput::make('name')->label(__('Name')),
                                ])->columns(1),
                        ]),

                    TagsInput::make('serial_number')
                        ->label(__('Serial numbers'))
                        ->placeholder(__('Enter SN and press Enter or comma'))
                        ->separator(',')
                        ->splitKeys(['Enter', ',', ' ', 'Tab'])
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                            if (! empty($state)) {
                                $snCount = is_array($state) ? count($state) : count(explode(',', $state));
                                $set('quantity', $snCount);
                                $price = (float) $get('price_per_unit');
                                $set('total_price', $price * $snCount);
                            }
                        })
                        ->columnSpanFull(),
                ])->columns(1),

                Section::make('Финансы и учет')->schema([
                    TextInput::make('quantity')
                        ->label('Количество')
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->live(onBlur: true) // Реактивность при потере фокуса
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                            $price = (float) $get('price');
                            $set('total_price', $price * (float) $state);
                        }),

                    TextInput::make('price')
                        ->label('Цена за шт. (UAH)')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                            $qty = (float) $get('quantity');
                            $set('total_price', $qty * (float) $state);
                        }),

                    TextInput::make('total_price')
                        ->label('Общая сумма (UAH)')
                        ->numeric()
                        ->dehydrated(false)
                        ->readOnly()
                        ->extraInputAttributes(['style' => 'font-weight: bold; background-color: #f3f4f6;']),
                ])->columns(1),

                Section::make('photo')->schema([
                    SpatieMediaLibraryFileUpload::make('photos')
                        ->collection('inventory')
                        ->label(__('Photo'))
                        ->multiple()
                        ->automaticallyResizeImagesMode('cover')
                        ->imagePreviewHeight(350)
                        ->customProperties(['format' => 'webp', 'width' => 1800, 'quality' => 50])
                        ->openable()
                        ->preserveFilenames(),

                ]),
                Section::make('Дополнительно')->schema([
                    Textarea::make('comment')
                        ->label('Комментарий')
                        ->columnSpanFull(),
                ]),
            ]);
    }
}
