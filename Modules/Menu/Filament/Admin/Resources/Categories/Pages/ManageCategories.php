<?php

namespace Modules\Menu\Filament\Admin\Resources\Categories\Pages;

use App\Http\Controllers\PosterApiController;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Modules\Menu\Filament\Admin\Resources\Categories\CategoryResource;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('update')
                ->label(__('Update menu'))
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    (new PosterApiController)->getCategories(Filament::getTenant()->id);
                    //  (new PosterApiController)->getProducts(Filament::getTenant()->id);
                    Notification::make()
                        ->title(__('Updated successfully'))
                        ->icon('heroicon-o-qr-code')
                        ->iconColor('success')
                        ->send();
                }),
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [

        ];
    }
}
