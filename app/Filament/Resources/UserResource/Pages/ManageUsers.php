<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

use App\Models\User;
use Filament\Forms\Components\{Select, Hidden};
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Attach')
            ->label(__('Attach user'))
            ->modalHeading(__('Attach user'))
            ->requiresConfirmation()
            ->form([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                Select::make('user_id')->options(
                    User::whereDoesntHave('places', function ($query) {
                        $query->where('place_id', 1);
                    })
                    ->get()
                    ->pluck('name', 'id')
                )
                ->native(false)
            ])
            ->action(function (array $data): void {
                $user = User::findOrFail($data['user_id']);
                $user->place()->attach(Filament::getTenant()->id);
                    Notification::make()
                        ->title(__('User added to place'))
                        ->icon('heroicon-o-user')
                        ->iconColor('success')
                        ->send();
            }),
        ];
    }
}
