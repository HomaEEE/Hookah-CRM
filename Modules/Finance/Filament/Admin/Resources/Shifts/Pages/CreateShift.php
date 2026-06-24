<?php

namespace Modules\Finance\Filament\Admin\Resources\Shifts\Pages;

use App\Http\Controllers\PosterApiController;
use App\Models\Place;
use Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Wizard\Step;
use Modules\Finance\Enums\BillType;
use Modules\Finance\Enums\TransactionType;
use Modules\Finance\Filament\Admin\Resources\Shifts\Schemas\PosterWizard;
use Modules\Finance\Filament\Admin\Resources\Shifts\Schemas\ShiftWizard;
use Modules\Finance\Filament\Admin\Resources\Shifts\Schemas\WagesWizard;
use Modules\Finance\Filament\Admin\Resources\Shifts\ShiftResource;
use Modules\Finance\Models\Transaction;

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

        Transaction::create(
            [
                'user_id' => Auth::user()->id,
                'place_id' => $shift->place_id,
                'expense_category_id' => $wage->expense,
                'sum' => $shift->wages,
                'bill_id' => BillType::CASH->value,
                'added_at' => $shift->created_at,
                'comment' => 'Зарплаты #'.$shift->id.' за '.$shift->created_at->format('d/m/Y'),
            ]
        );

        Transaction::create(
            [
                'user_id' => Auth::user()->id,
                'place_id' => $shift->place_id,
                'type' => TransactionType::Income->value,
                'sum' => (int) $shift->terminal * 0.987,
                'bill_id' => BillType::CARD->value,
                'added_at' => $shift->created_at,
                'comment' => 'Выручка по терминалу #'.$shift->id.' за '.$shift->created_at->format('d/m/Y'),
            ]
        );

        Transaction::create(
            [
                'user_id' => Auth::user()->id,
                'place_id' => $shift->place_id,
                'type' => TransactionType::Income->value,
                'sum' => $shift->cash,
                'bill_id' => BillType::CASH->value,
                'added_at' => $shift->created_at,
                'comment' => 'Выручка наличными #'.$shift->id.' за '.$shift->created_at->format('d/m/Y'),
            ]
        );

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
