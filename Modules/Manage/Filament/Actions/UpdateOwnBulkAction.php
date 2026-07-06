<?php

namespace Modules\Manage\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Database\Eloquent\Collection;
use Modules\Manage\Enums\OwnEnum;

class UpdateOwnBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'update_own';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Владелец')
            ->icon('heroicon-o-user-plus')
            ->color('info')
            ->form([
                ToggleButtons::make('own')
                    ->options(OwnEnum::class)
                    ->grouped(),
            ])
            ->action(function (Collection $records, array $data): void {
                // Обновляем только владельца
                $records->first()->query()
                    ->whereIn('id', $records->pluck('id'))
                    ->update([
                        'own' => $data['own'],
                    ]);
            })
            ->deselectRecordsAfterCompletion()
            ->successNotificationTitle('Владелец успешно назначен!');
    }
}
