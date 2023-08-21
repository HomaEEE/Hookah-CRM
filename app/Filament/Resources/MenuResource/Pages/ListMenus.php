<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use App\Http\Controllers\PosterApiController as PosterApi;
use Filament\Notifications\Notification;

use App\Models\Place;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;

class ListMenus extends ListRecords
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('update')
                ->label(__('Update menu'))
                ->form([
                    Select::make('place_id')
                        ->label(__('Place'))
                        ->options(
                            Place::all()->pluck('nameCity', 'id')
                        )
                ])
                ->action(function (array $data): void {
                    if (blank($data['place_id'])) {
                        Notification::make()
                        ->title(__('Please, select place'))
                        ->icon('heroicon-o-qr-code')
                        ->iconColor('danger')
                        ->send();
                    } else {
                        (new PosterApi())->getCategories($data['place_id']);
                        (new PosterApi())->getProducts($data['place_id']);
                        Notification::make()
                            ->title(__('Updated successfully'))
                            ->icon('heroicon-o-qr-code')
                            ->iconColor('success')
                            ->send();
                    }
                }),
        ];
    }
}
