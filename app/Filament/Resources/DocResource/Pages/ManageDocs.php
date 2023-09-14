<?php

namespace App\Filament\Resources\DocResource\Pages;

use App\Filament\Resources\DocResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageDocs extends ManageRecords
{
    protected static string $resource = DocResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->slideOver(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'contracts' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 5))
        ];
    }
}
