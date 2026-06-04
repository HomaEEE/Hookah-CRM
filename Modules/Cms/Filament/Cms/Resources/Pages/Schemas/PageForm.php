<?php

namespace Modules\Cms\Filament\Cms\Resources\Pages\Schemas;

use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;
use Awcodes\Mason\Mason;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tab::make('General')->schema([
                    TranslatableTabs::make()
                        ->addEmptyBadgeWhenAllFieldsAreEmpty(emptyLabel: __('locale empty'))
                        ->schema([
                            TextInput::make('title')->label(__('Title')),
                            TextInput::make('description')->label(__('Description')),
                            TextInput::make('keywords')->label(__('Keywords')),
                        ]),
                ]),
                Tab::make('Content')->schema([
                    Mason::make('content'),

                ]),
            ]);
    }
}
