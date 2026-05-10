<?php

namespace Modules\Manage\Filament\Admin\Resources\Writeoffs\Pages;

use Filament\Actions\CreateAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Manage\Filament\Admin\Resources\Writeoffs\WriteoffResource;

class ManageWriteoffs extends ManageRecords
{
    protected static string $resource = WriteoffResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('workshop.name')->hiddenLabel(),
                        TextEntry::make('user.name')->hiddenLabel(),
                        TextEntry::make('created_at')->hiddenLabel()->badge()->dateTime('d/m/Y'),
                    ])->columns(3),
                RepeatableEntry::make('whiteoff')
                    ->schema([
                        TextEntry::make('ingredient.ingredient'),
                        TextEntry::make('quantity'),
                        TextEntry::make('ingredient.unit'),
                    ])
                    ->columns(3),
            ]);
    }
}
