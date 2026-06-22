<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Dashboard\PosterShift;
use App\Http\Controllers\PosterApiController;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Finance\Filament\Admin\Resources\Shifts\ShiftResource;
use Modules\Finance\Filament\Admin\Resources\Transactions\Schemas\TransactionForm;
use Modules\Finance\Filament\Admin\Widgets\TodayShift;
use Modules\Finance\Models\Transaction;
use Modules\Manage\Filament\Admin\Resources\Supplies\Schemas\SupplyForm;
use Modules\Manage\Models\Supply;

class Dashboard extends \Filament\Pages\Dashboard
{
    // // use HasPageShield;

    // public static function getPermissionPrefixes(): array
    // {
    //     return [
    //         'can_view_dash',
    //         'can_delete_bill',
    //         'can_open_shift',
    //     ];
    // }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected string $view = 'filament.pages.dashboard';

    protected static ?string $slug = 'dashboard';

    public function getTitle(): string|Htmlable
    {
        return __('Dashboard');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TodayShift::class,
            PosterShift::class,
        ];
    }

    protected function getActions(): array
    {
        return [

            ActionGroup::make([

                CreateAction::make('add_supply')->label(__('Supply'))
                    ->model(Supply::class)
                    ->createAnother(false)
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->modalHeading(__('Add Supply'))
                    ->modalWidth('md')
                    ->keyBindings(['command+s', 'ctrl+s'])
                    ->modalIcon('heroicon-o-inbox-arrow-down')
                    ->slideOver()
                    ->form(SupplyForm::configure(...))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->icon('heroicon-o-inbox-arrow-down')
                            ->title(__('Supply added'))
                    ),

                CreateAction::make('add_transaction')->label(__('Transaction'))
                    ->model(Transaction::class)

                    ->icon('heroicon-o-document-minus')
                    ->modalHeading(__('Add Transaction'))
                    ->modalWidth('md')
                    ->slideOver()
                    ->modalIcon('heroicon-o-document-minus')
                    ->keyBindings(['command+s', 'ctrl+s'])
                    ->form(TransactionForm::configure(...))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('Transaction added'))
                            ->icon('heroicon-o-document-minus')
                    ),

            ])->button()->label(__('Add'))->color('success')->icon('heroicon-o-squares-plus'),

            // Action::make('addTips')
            //     ->label(__('Add Tips'))
            //     ->color('success')
            //     ->modalWidth('xs')
            //     ->icon('heroicon-o-banknotes')
            //     ->modalHeading(__('Add Tips'))
            //     ->keyBindings(['command+s', 'ctrl+s'])
            //     ->requiresConfirmation()
            //     ->schema([
            //         Hidden::make('place_id')->default(Filament::getTenant()->id),
            //         TextInput::make('tips')->label('')
            //             ->numeric()
            //             ->mask(RawJs::make('$money($input)'))
            //             ->stripCharacters(',')
            //             ->required(),
            //     ])
            //     ->action(function (array $data): void {
            //         Tip::updateOrCreate(
            //             ['created_at' => Carbon::today(), 'place_id' => $data['place_id']],
            //         )->increment('tips', $data['tips'] * 100);

            //         Notification::make()
            //             ->title(__('Tips added'))
            //             ->icon('heroicon-o-banknotes')
            //             ->iconColor('success')
            //             ->send();
            //     }),
            ActionGroup::make([

                Action::make('openShift')
                    ->label(__('Open Shift'))
                    ->icon('heroicon-o-lock-open')
                    ->modalHeading(__('Open Shift'))
                    ->requiresConfirmation()
                    ->schema([
                        Hidden::make('place_id')->default(Filament::getTenant()->id),
                    ])
                    ->action(function (array $data): void {
                        (new PosterApiController)->openShift($data);

                        Notification::make()
                            ->title(__('Shift opened'))
                            ->icon('heroicon-o-banknotes')
                            ->iconColor('success')
                            ->send();
                    }),

                Action::make('deleteReceipt')
                    ->label(__('Delete Receipt'))
                    ->icon('heroicon-o-backspace')
                    ->modalWidth('xs')

                    ->modalHeading(__('Delete Receipt'))
                    ->requiresConfirmation()
                    ->schema([
                        Hidden::make('place_id')->default(Filament::getTenant()->id)
                            ->required()
                            ->live(),
                        TextInput::make('receipt_id')->numeric()->label(__('#'))->required(),
                    ])
                    ->action(function (array $data): void {
                        (new PosterApiController)->deleteReceipt($data);
                    }),

                Action::make('closeShift')
                    ->label(__('Close Shift'))
                    ->icon('heroicon-o-lock-closed')
                    ->url(fn (): string => ShiftResource::getUrl(
                        'create',
                        [
                            'tenant' => Filament::getTenant(),
                        ],
                        panel: 'admin',
                    )),
                // Action::make('onShift')
                //     ->label(__('On Shift'))
                //     ->color('info')
                //     ->icon('heroicon-o-user-plus')
                //     ->modalHeading(__('On Shift'))
                //     ->modalWidth('sm')
                //     ->requiresConfirmation()
                //     ->form([
                //         Hidden::make('place_id')->default(Filament::getTenant()->id)
                //             ->required()
                //             ->reactive(),
                //         Select::make(;)
                //     ])
                //     ->action(function (array $data): void {
                //         (new PosterApiController())->openShift($data);
                //     }),

            ])
                ->button()->label(__('Shift'))->icon('heroicon-o-inbox-stack')->color('info'),

        ];

    }
}
