<?php

namespace Modules\Inventory\Filament\Resources\Inventories\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Item')->schema([

                    TextInput::make('name')->translateLabel()->required(),
                    TextInput::make('count')->translateLabel(),
                    TextInput::make('price')->translateLabel(),   
                    TextInput::make('sn')->translateLabel(),   
                                        Textarea::make('comment')->translateLabel(),   

                    ])->columns(1),
                SpatieMediaLibraryFileUpload::make('photo')
                    ->label(__('Photo'))
                    ->collection('inventories')
                    ->multiple()
                    ->imageResizeMode('cover')
                    ->imagePreviewHeight(350)
                    ->customProperties(['format' => 'webp', 'width' => 1200, 'quality' => 70])
                    ->openable()
                    ->preserveFilenames()
                    ->reactive(),
            ]);
    }
}
