<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRecords;

class ManagePurchases extends ManageRecords
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()
                ->columns(3)
                ->schema([
                    TextEntry::make('supplier.company')->hiddenLabel(),
                    TextEntry::make('status')->hiddenLabel(),
                    TextEntry::make('created_at')->hiddenLabel(),
                ]),
            RepeatableEntry::make('ingredients')
                ->schema([
                    TextEntry::make('ingredient'),
                    TextEntry::make('quantity'),
                ])
                ->columns(2),
        ]);
    }
}
