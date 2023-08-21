<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use Auth;
use App\Filament\Resources\SupplierResource;
use App\Models\Place;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ManageRecords;

class ManageSuppliers extends ManageRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {

        $tabs = [null => Tab::make(__('All'))];

        $places = Auth::user()->place;

        foreach ($places as $place) {
            $tabs[$place->city] = Tab::make()->query(function ($query) use ($place) {
                $query->where('place_id', $place->id);
            });
        }

        return $tabs;
    }
}
