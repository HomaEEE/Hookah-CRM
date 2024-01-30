<?php

namespace App\Filament\Resources\TechcardResource\Pages;

use App\Filament\Resources\TechcardResource;
use App\Models\Workshop;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Auth;
use Filament\Facades\Filament;
use Filament\Resources\Components\Tab;

class ManageTechcards extends ManageRecords
{
    protected static string $resource = TechcardResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [null => Tab::make(__('All'))];

        $workshops = Workshop::where('place_id', Filament::getTenant()->id)->get();

        foreach ($workshops as $workshop) {
            $tabs[$workshop->name] = Tab::make()->query(function ($query) use ($workshop) {
                $query->where('workshop_id', $workshop->id);
            });
        }

        return $tabs;
    }
}
