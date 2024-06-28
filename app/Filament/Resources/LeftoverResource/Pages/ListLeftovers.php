<?php

namespace App\Filament\Resources\LeftoverResource\Pages;

use App\Filament\Resources\LeftoverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeftovers extends ListRecords
{
    protected static string $resource = LeftoverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
