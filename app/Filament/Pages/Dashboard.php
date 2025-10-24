<?php

namespace App\Filament\Pages;

use App\Filament\Components\Forms\ExpenseForm;
use App\Filament\Components\Forms\IncomeForm;
use App\Filament\Components\Forms\SupplyForm;
use App\Filament\Components\Forms\WithdrawalForm;
use App\Filament\Widgets\Dashboard\PosterShift;
use App\Filament\Widgets\Dashboard\TodayShift;
use App\Http\Controllers\PosterApiController;
use App\Models\Income;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends \Filament\Pages\Dashboard  // implements HasShieldPermissions
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

    protected static ?int $navigationSort = 1;

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

                CreateAction::make('add_supply')->label(__('Expense'))
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->modalHeading(__('Add Supply'))
                    ->createAnother(false)
                    ->modalWidth('md')
                    ->keyBindings(['command+s', 'ctrl+s'])
                    ->modalIcon('heroicon-o-inbox-arrow-down')
                    ->slideOver()
                    ->schema([
                        ToggleButtons::make('type')->label(__('Type'))
                            ->options(
                                [
                                    0 => __('Supply'),
                                    1 => __('Expense'),
                                ]
                            )
                            ->colors([
                                0 => 'info',
                                1 => 'success',
                            ])
                            ->default(0)
                            ->inline()
                            ->live()
                            ->grouped()
                            ->extraAttributes(['style' => 'width: 100%'])
                            ->reactive(),
                        SupplyForm::schema(),
                        ExpenseForm::schema(),
                    ]
                    )
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->icon('heroicon-o-inbox-arrow-down')
                            ->title(__('Supply added'))
                    ),

                CreateAction::make('addIncome')->label(__('Income'))
                    ->model(Income::class)
                    ->icon('heroicon-o-plus')
                    ->modalHeading(__('Add Income'))
                    ->createAnother(false)
                    ->modalWidth('xs')
                    ->keyBindings(['command+s', 'ctrl+s'])
                    ->modalIcon('heroicon-o-inbox-arrow-down')
                    ->schema(
                        IncomeForm::schema()
                    )
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->icon('heroicon-o-plus-circle')
                            ->title(__('Income added'))
                    ),

                CreateAction::make('addWithdraval')->label(__('Withdraval'))
                    ->model(Income::class)
                    ->icon('heroicon-o-minus')
                    ->modalHeading(__('Add Withdraval'))
                    ->createAnother(false)
                    ->modalWidth('sm')
                    ->keyBindings(['command+s', 'ctrl+s'])
                    ->modalIcon('heroicon-o-inbox-arrow-down')
                    ->schema(
                        WithdrawalForm::schema()
                    )
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->icon('heroicon-o-minus')
                            ->title(__('Withdraval added'))
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
                Action::make('closeShift')
                    ->label(__('Close Shift'))
                    ->icon('heroicon-o-lock-closed')
                    ->modalHeading(__('Close Shift'))
                    ->requiresConfirmation()
                    ->schema([
                        Hidden::make('place_id')->default(Filament::getTenant()->id),
                    ])
                    ->action(function (array $data): void {
                        (new PosterApiController)->openShift($data);

                        Notification::make()
                            ->title(__('Shift closed'))
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
                            ->reactive(),
                        TextInput::make('receipt_id')->numeric()->label(__('#'))->required(),
                    ])
                    ->action(function (array $data): void {
                        (new PosterApiController)->deleteReceipt($data);
                    }),
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
