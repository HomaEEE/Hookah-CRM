<?php

namespace Modules\Place\Filament\Admin\Resources\Generators\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Modules\Place\Filament\Admin\Resources\Generators\GeneratorResource;

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
