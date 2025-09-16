<?php

namespace App\Filament\Resources\IngredientResource\Pages;

use App\Filament\Resources\IngredientResource;
use App\Http\Controllers\PosterApiController;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageIngredients extends ManageRecords
{
    protected static string $resource = IngredientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('update')
            ->label(__('Update ingredients'))
            ->icon('heroicon-o-arrow-path')
            ->action(function (): void {
                    (new PosterApiController())->getIngredients(Filament::getTenant()->id);
                    Notification::make()
                        ->title(__('Updated successfully'))
                        ->icon('heroicon-o-qr-code')
                        ->iconColor('success')
                        ->send();
            }),
        ];
    }
}
