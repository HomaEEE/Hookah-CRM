<?php

namespace Modules\Finance\Filament\Admin\Resources\Shifts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Finance\Filament\Admin\Resources\Shifts\ShiftResource;

class ListShifts extends ListRecords
{
    protected static string $resource = ShiftResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }

    // protected function getTableQuery(): Builder
    // {
    //     return Shift::query()->whereIn('place_id', Auth::user()->place->pluck('id'));
    // }

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
