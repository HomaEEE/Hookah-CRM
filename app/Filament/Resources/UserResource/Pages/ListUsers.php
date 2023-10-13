<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
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
                        User::doesnthave('place')->get()
                            ->pluck('name', 'id')
                    )
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
