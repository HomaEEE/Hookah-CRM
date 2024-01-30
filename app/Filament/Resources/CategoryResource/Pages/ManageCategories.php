<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Http\Controllers\PosterApiController;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('update')
            ->label(__('Update menu'))
            ->action(function (): void {
                    (new PosterApiController())->getCategories(Filament::getTenant()->id());
                    (new PosterApiController())->getProducts(Filament::getTenant()->id());
                    Notification::make()
                        ->title(__('Updated successfully'))
                        ->icon('heroicon-o-qr-code')
                        ->iconColor('success')
                        ->send();
            }),
            Actions\CreateAction::make(),
        ];
    }
}
