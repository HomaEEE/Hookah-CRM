<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Auth;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageTasks extends ManageRecords
{
    protected static string $resource = TaskResource::class;

    // protected function getTableQuery(): Builder
    // {
    //     return Task::query()->whereIn('place_id', Auth::user()->place->pluck('id'));
    //     // ->where(function($query) {
    //     //     $query->where('for_user_id', Auth::user()->id)
    //     //         ->orWhere('for_user_id', null);
    //     // });
    // }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->slideOver(),
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
