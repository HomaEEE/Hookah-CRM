<?php

namespace App\Filament\Clusters\Data\Resources\ReqvizitesResource\Pages;

use App\Filament\Clusters\Data\Resources\ReqvizitesResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRecords;

class ManageReqvizites extends ManageRecords
{
    protected static string $resource = ReqvizitesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('supplier.company')->hiddenLabel(),
            TextEntry::make('IBAN')->badge()->copyable(),
            TextEntry::make('INN')->badge()->copyable(),
            TextEntry::make('own')->translateLabel()->copyable(),
        ])->columns(1);
    }
}
