<?php

namespace App\Filament\Resources\Products\Schemas;

use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;
use App\Models\Category;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make()
                    ->contained(false)
                    ->columns([
                        'default' => 3,
                        'md' => 3,
                        'xl' => 3,
                    ])
                    ->schema([
                        ToggleButtons::make('visible')->label(__('Visible'))->inline()->boolean()->grouped()->columns(1),
                        ToggleButtons::make('visible_menu')->label(__('Visible menu'))->inline()->boolean()->grouped()->columns(1),
                    ]),

                TextInput::make('product_name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255)
                    ->columns(1),

                Select::make('menu_category_id')
                    ->label(__('Category'))
                    ->options(Category::where('place_id', Filament::getTenant()->id)->pluck('category_name', 'category_id'))
                    ->searchable(),

                TranslatableTabs::make()
                    ->addEmptyBadgeWhenAllFieldsAreEmpty(emptyLabel: __('locale empty'))
                    ->schema([
                        TextInput::make('frontend_name')->label(__('Front product name')),
                        Textarea::make('frontend_desc')->label(__('Description')),
                    ]),
                TextInput::make('sup')->label(__('sup')),
                TextInput::make('barcode')->label('Barcode'),

                TextInput::make('out')->label(__('Out'))->numeric()
                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                TextInput::make('price')->label(__('Price'))->numeric()
                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                    ->required(),
                Textarea::make('ingredients')->label(__('Ingredients'))
                    ->visible(fn ($record) => ! empty($record['ingredients']) ? '1' : '0'),

                SpatieMediaLibraryFileUpload::make('media')
                    ->collection('products')
                    ->image()
                    ->imagePreviewHeight('700')
                    ->imageResizeTargetWidth('700')
                    ->imageResizeMode('contain'),
            ])->columns(1);
    }
}
