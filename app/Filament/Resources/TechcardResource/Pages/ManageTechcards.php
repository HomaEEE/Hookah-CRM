<?php

namespace App\Filament\Resources\TechcardResource\Pages;

use App\Filament\Resources\TechcardResource;
use App\Http\Controllers\PosterApiController;
use App\Models\Workshop;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Tables\Actions\CreateAction;

class ManageTechcards extends ManageRecords
{
    protected static string $resource = TechcardResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('update')
            ->label(__('Update techcards'))
            ->icon('heroicon-o-arrow-path')
            ->action(function (): void {
                    (new PosterApiController())->getTechcards(Filament::getTenant()->id);
                    Notification::make()
                        ->title(__('Updated successfully'))
                        ->icon('heroicon-o-qr-code')
                        ->iconColor('success')
                        ->send();
            }),
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
