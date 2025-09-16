<?php

namespace App\Filament\Resources\Storages\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\ItemExporter;
use App\Filament\Imports\ItemImporter;
use App\Filament\Resources\Storages\StorageResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStorages extends ManageRecords
{
    protected static string $resource = StorageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            ImportAction::make()
            ->importer(ItemImporter::class),
            ExportAction::make()
            ->exporter(ItemExporter::class),
        ];
    }


}
