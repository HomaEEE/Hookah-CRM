<?php

namespace App\Filament\Resources\Shifts\Pages;

use Filament\Schemas\Components\Wizard\Step;
use App\Filament\Components\Shift\PosterWizard;
use App\Filament\Components\Shift\ShiftWizard;
use App\Filament\Components\Shift\WagesWizard;
use App\Filament\Resources\Shifts\ShiftResource;
use App\Http\Controllers\PosterApiController;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\Place;
use Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;

class CreateShift extends CreateRecord
{
    use HasWizard;

    protected static string $resource = ShiftResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['terminal'] = $data['terminal'] * 0.987;

        return $data;
    }

    protected function afterCreate(): void
    {
        $shift = $this->record;

        $wage = Place::where('id', $shift->place_id)->select('settings->wage_expense as expense')->first();

        (new PosterApiController)->closeShift($shift);

        Expense::create(
            [
                'user_id' => Auth::user()->id,
                'place_id' => $shift->place_id,
                'category_id' => $wage->expense,
                'sum' => $shift->wages,
                'bill_id' => '0',
                'comment' => 'Зарплаты',
            ]
        );

        Bill::updateOrCreate(
            ['id' => 0],
            ['balance' => (int) round(($shift->shift * 0.987), 0)]
        );

        Bill::updateOrCreate(
            ['id' => 1],
        )->increment('balance', $shift->terminal);

        Notification::make()
            ->title(__('Shift are closed'))
            ->send();
    }

    protected function getSteps(): array
    {

        return [

            Step::make('Poster')->translateLabel()
                ->completedIcon('heroicon-m-briefcase')
                ->schema(
                    PosterWizard::schema()
                ),
            Step::make('Wages')->translateLabel()
                ->completedIcon('heroicon-m-user-plus')
                ->schema(
                    WagesWizard::schema()

                ),
            Step::make('Shift')->translateLabel()
                ->completedIcon('heroicon-m-document-check')
                ->schema(
                    ShiftWizard::schema()
                ),
        ];
    }
}
