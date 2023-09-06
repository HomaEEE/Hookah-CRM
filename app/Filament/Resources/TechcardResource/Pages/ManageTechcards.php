<?php

namespace App\Filament\Resources\TechcardResource\Pages;

use App\Filament\Resources\TechcardResource;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Auth;

class ManageTechcards extends ManageRecords
{
    protected static string $resource = TechcardResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // public function getTabs(): array
    // {

    //     $tabs = [null => Tab::make(__('All'))];

    //     $places = Auth::user()->place;

    //     foreach ($places as $place) {
    //         $tabs[$place->city] = Tab::make()->query(function ($query) use ($place) {
    //             $query->where('place_id', $place->id);
    //         });
    //     }

    //     return $tabs;
    // }
}
