<?php

namespace App\Filament\Clusters\Data\Resources\Reqvizites\Pages;

use Filament\Actions\CreateAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Schema;
use Modules\Place\Filament\Admin\Resources\Reqvizites\ReqvizitesResource;

class ManageReqvizites extends ManageRecords
{
    protected static string $resource = ReqvizitesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('supplier.company')->hiddenLabel(),
            TextEntry::make('IBAN')->badge()->copyable(),
            TextEntry::make('INN')->badge()->copyable(),
            TextEntry::make('own')->translateLabel()->copyable(),
        ])->columns(1);
    }
}
