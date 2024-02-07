<?php

namespace App\Filament\Resources\StorageResource\Pages;

use App\Filament\Exports\ItemExporter;
use App\Filament\Imports\ItemImporter;
use App\Filament\Resources\StorageResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStorages extends ManageRecords
{
    protected static string $resource = StorageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\ImportAction::make()
            ->importer(ItemImporter::class),
            Actions\ExportAction::make()
            ->exporter(ItemExporter::class),
        ];
    }


}
