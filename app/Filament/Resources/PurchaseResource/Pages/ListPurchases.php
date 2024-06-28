<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use Auth;
use App\Filament\Resources\PurchaseResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

use App\Http\Controllers\PosterApiController as PosterApi;

class ListPurchases extends ListRecords
{
    protected static string $resource = PurchaseResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('update')
                ->label(__('Update Ingredients'))
                ->slideOver()
                ->modalHeading(__('Update Ingredients'))
                ->requiresConfirmation()

                ->action(function (): void {
                        (new PosterApi())->getIngredients(Filament::getTenant()->id);
                        Notification::make()
                            ->title(__('Updated successfully'))
                            ->icon('heroicon-o-squares-plus')
                            ->iconColor('success')
                            ->send();
                    }
                ),
        ];
    }
}
