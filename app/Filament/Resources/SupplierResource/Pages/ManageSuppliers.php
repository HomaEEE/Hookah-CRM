<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Enums\SupplierType;
use App\Filament\Resources\SupplierResource;
use App\Http\Controllers\PosterApiController;
use App\Models\Supplier;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;

use function PHPSTORM_META\type;

class ManageSuppliers extends ManageRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('update')
            ->label(__('Update suppliers'))
            ->icon('heroicon-o-arrow-path')
            ->action(function (): void {
                    (new PosterApiController())->getSuppliers(Filament::getTenant()->id);
                    Notification::make()
                        ->title(__('Updated successfully'))
                        ->icon('heroicon-o-users')
                        ->iconColor('success')
                        ->send();
            }),
            Actions\CreateAction::make()->createAnother(false),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [null => Tab::make(__('All'))];

        $types = SupplierType::cases();
        foreach ($types as $type) {
            $tabs[__($type->name)] = Tab::make()->query(function ($query) use ($type) {
                $query->where('type', $type->value);
            });
        }

        return $tabs;
    }
}
