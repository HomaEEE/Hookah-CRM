<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\ProductResource;
use App\Http\Controllers\PosterApiController;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageProducts extends ManageRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('update')
            ->label(__('Update menu'))
            ->icon('heroicon-o-arrow-path')
            ->action(function (): void {
                    (new PosterApiController())->getCategories(Filament::getTenant()->id);
                    (new PosterApiController())->getProducts(Filament::getTenant()->id);
                    Notification::make()
                        ->title(__('Updated successfully'))
                        ->icon('heroicon-o-qr-code')
                        ->iconColor('success')
                        ->send();
            }),
            Actions\ExportAction::make()
            ->exporter(ProductExporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
