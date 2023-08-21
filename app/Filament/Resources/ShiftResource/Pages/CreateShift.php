<?php

namespace App\Filament\Resources\ShiftResource\Pages;

use App\Filament\Resources\ExpenseResource;
use App\Filament\Resources\ShiftResource;
use App\Http\Controllers\PosterApiController;
use App\Models\Expense;
use App\Models\Place;
use App\Models\Shift;
use Auth;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Actions\Action;
use Filament\Forms\Components\Wizard\Step;

use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;

class CreateShift extends CreateRecord
{

    use HasWizard;

    protected static string $resource = ShiftResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $shift = $this->record;

        $wage = Place::where('id', $shift->place_id)->select('settings->wage_expense as expense')->first();

        Expense::create(
            [
                'user_id' => Auth::user()->id,
                'place_id' => $shift->place_id,
                'category_id' => $wage->expense,
                'sum' => $shift->wages,
                'type_pay' => '0',
                'comment' => 'Зарплаты',
            ]
        );

        Notification::make()
            ->title(__('Wages added in expenses'))
            ->send();

        (new PosterApiController())->closeShift($shift);

        Notification::make()
            ->title(__('Shift are closed'))
            ->send();


    }

    protected function getSteps(): array
    {
        return [
            Step::make('Reoprts')->translateLabel()
                ->schema([
                    Section::make(ShiftResource::getFormSchema('report')),
                ]),
            Step::make('Poster')->translateLabel()
                ->schema(
                    ShiftResource::getFormSchema('poster')
                ),
            Step::make('Wages')->translateLabel()
                ->schema(
                    ShiftResource::getFormSchema('wages')
                ),
            Step::make('Shift')->translateLabel()
                ->schema(
                   ShiftResource::getFormSchema('shift')
                ),
        ];
    }

}
