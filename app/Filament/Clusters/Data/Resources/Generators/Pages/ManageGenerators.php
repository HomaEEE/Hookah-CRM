<?php

namespace App\Filament\Clusters\Data\Resources\Generators\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Clusters\Data\Resources\Generators\GeneratorResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGenerators extends ManageRecords
{
    protected static string $resource = GeneratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['load'] = $data['load'] * 100;

                    return $data;
                }),
        ];
    }
}
