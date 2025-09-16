<?php

namespace App\Filament\Resources\WriteoffResource\Pages;

use App\Filament\Resources\WriteoffResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageWriteoffs extends ManageRecords
{
    protected static string $resource = WriteoffResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
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
