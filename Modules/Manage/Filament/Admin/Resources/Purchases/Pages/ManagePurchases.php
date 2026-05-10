<?php

namespace Modules\Manage\Filament\Admin\Resources\Purchases\Pages;

use Filament\Actions\CreateAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Manage\Filament\Admin\Resources\Purchases\PurchaseResource;

class ManagePurchases extends ManageRecords
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
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
