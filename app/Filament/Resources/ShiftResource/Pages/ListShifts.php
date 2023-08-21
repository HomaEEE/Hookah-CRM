<?php

namespace App\Filament\Resources\ShiftResource\Pages;

use App\Filament\Resources\ShiftResource;
use App\Models\Shift;
use Auth;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListShifts extends ListRecords
{
    protected static string $resource = ShiftResource::class;

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
