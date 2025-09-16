<?php

namespace App\Filament\Resources\Leftovers\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Leftovers\LeftoverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeftovers extends ListRecords
{
    protected static string $resource = LeftoverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    // public function getTabs(): array
    // {
    //     // return [
    //     //     'new' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'new')),
    //     //     'processing' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'processing')),
    //     //     'shipped' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'shipped')),
    //     // ];
    // }
}
