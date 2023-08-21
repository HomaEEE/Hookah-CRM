<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use Auth;
use App\Filament\Resources\PurchaseResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListPurchases extends ListRecords
{
    protected static string $resource = PurchaseResource::class;

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
