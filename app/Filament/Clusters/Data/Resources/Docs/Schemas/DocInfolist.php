<?php

namespace App\Filament\Clusters\Data\Resources\Docs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DocInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')->schema([
                    TextEntry::make('name')->translateLabel(),
                    TextEntry::make('type')->translateLabel(),
                    TextEntry::make('date')->translateLabel(),

                ])->columnSpanFull()->columns(3),
            ]);
    }
}
