<?php

namespace App\Filament\Clusters\Settings\Resources\Users\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use App\Filament\Clusters\Settings\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('Attach')
                ->label(__('Attach user'))
                ->modalHeading(__('Attach user'))
                ->requiresConfirmation()
                ->schema([
                    Hidden::make('place_id')->default(Filament::getTenant()->id),
                    Select::make('user_id')->options(
                        User::whereDoesntHave('places', function ($query) {
                            $query->where('place_id', 1);
                        })
                            ->get()
                            ->pluck('name', 'id')
                    )
                        ->native(false),
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
