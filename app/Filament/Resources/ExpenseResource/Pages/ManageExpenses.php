<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use App\Models\Expense;
use Auth;
use Closure;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ManageRecords;

class ManageExpenses extends ManageRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sum'] = $data['sum'] * 100;

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['sum'] = $data['sum'] / 100;

        return $data;
    }

    protected function getActions(): array
    {
        return [
            CreateAction::make()->slideOver(),
        ];
    }

    protected function getTableRecordActionUsing(): ?Closure
    {
        return fn () => 'view';
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
