<?php

namespace App\Filament\Clusters\Data\Resources\GeneratorResource\Pages;

use App\Filament\Clusters\Data\Resources\GeneratorResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGenerators extends ManageRecords
{
    protected static string $resource = GeneratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['load'] = $data['load'] * 100;

                    return $data;
                }),
        ];
    }
}
