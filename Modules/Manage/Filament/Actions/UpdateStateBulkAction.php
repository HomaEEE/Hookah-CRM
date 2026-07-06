<?php

namespace Modules\Manage\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Collection;
use Modules\Manage\Enums\StateEnum;

class UpdateStateBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'update_state';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Изменить статус')
            ->icon('heroicon-o-check-badge') // Иконка статуса
            ->color('warning')
            ->form([
                Select::make('state')
                    ->label('Новый статус')
                    ->options(StateEnum::class)
                    ->placeholder('Выберите статус...')
                    ->required(),
            ])
            ->action(function (Collection $records, array $data): void {
                // Обновляем только статус
                $records->first()->query()
                    ->whereIn('id', $records->pluck('id'))
                    ->update([
                        'state' => $data['state'],
                    ]);
            })
            ->deselectRecordsAfterCompletion()
            ->successNotificationTitle('Статусы успешно обновлены!');
    }
}
