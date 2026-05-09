<?php

namespace Modules\Manage\Filament\Admin\Resources\Ingredients\Pages;

use App\Http\Controllers\PosterApiController;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Modules\Manage\Filament\Admin\Resources\Ingredients\IngredientResource;

class ManageIngredients extends ManageRecords
{
    protected static string $resource = IngredientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('update')
                ->label(__('Update ingredients'))
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    (new PosterApiController)->getIngredients(Filament::getTenant()->id);
                    Notification::make()
                        ->title(__('Updated successfully'))
                        ->icon('heroicon-o-qr-code')
                        ->iconColor('success')
                        ->send();
                }),
        ];
    }
}
