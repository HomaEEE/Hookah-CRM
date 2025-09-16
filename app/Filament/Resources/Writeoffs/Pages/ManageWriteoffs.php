<?php

namespace App\Filament\Resources\Writeoffs\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Filament\Resources\Writeoffs\WriteoffResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

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
                        TextEntry::make('workshop.name')->label(''),
                        TextEntry::make('user.name')->label(''),
                        TextEntry::make('created_at')->label('')->badge()->dateTime('d/m/Y'),
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
