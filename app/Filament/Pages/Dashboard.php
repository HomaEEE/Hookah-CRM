<?php

namespace App\Filament\Pages;

use App\Http\Controllers\PosterApiController;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

use App\Models\{Expense, ExpenseCategory, Tip};

use Carbon\Carbon;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $slug = 'dashboard';

    use HasPageShield;

    public function getTitle(): string | Htmlable
    {
        return __('Dashboard');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\Dashboard\TodayShift::class,
            \App\Filament\Widgets\Dashboard\PosterShift::class,
            \App\Filament\Widgets\Dashboard\TabWidget::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            // Action::make('onShift')
            //     ->label(__('On Shift'))
            //     ->size('lg')
            //     ->icon('heroicon-o-login')
            //     ->color('success')

            //     ->modalHeading(__('Add portions'))
            //     ->modalWidth('xs')

            //     ->form([
            //         TextInput::make('portion')
            //             ->label(__('Add portions'))->numeric()
            //             ->required(),
            //     ])
            //     ->action(function (ShiftUser $record, array $data): void {
            //         $record->update(
            //             [$record->portions += $data['portion']]
            //         );
            //     }),
            Action::make('addTips')
                ->label(__('Add Tips'))
                ->color('success')
                ->icon('heroicon-o-banknotes')
                ->modalHeading(__('Add Tips'))
                ->slideOver()
                ->requiresConfirmation()

                ->form([
                    Hidden::make('place_id')->default(Filament::getTenant()->id),
                    TextInput::make('tips')
                        ->label('')
                        ->numeric()
                        ->required()
                ])
                ->action(function (array $data): void {
                    Tip::updateOrCreate(
                        ['created_at' => Carbon::today(), 'place_id' => $data['place_id']],
                    )->increment('tips', $data['tips']);

                    Notification::make()
                        ->title(__('Tips added'))
                        ->icon('heroicon-o-banknotes')
                        ->iconColor('success')
                        ->send();
                }),

                // Action::make('add_expense')->label(__('Add Expense'))
                // ->icon('heroicon-s-banknotes')
                // ->color('info')
                // ->modalHeading(__('Add Expense'))
                // ->requiresConfirmation()
                // ->form([
                //         DateTimePicker::make('added_at')
                //             ->label(__('Date Added'))
                //             ->native(false)
                //             ->seconds(false)
                //             ->maxDate(now())->default(now()),
                //         Select::make('category_id')->label(__('Category'))
                //             ->relationship('categories', 'name')
                //             ->searchable()
                //             ->required()
                //             ->reactive()
                //             ->options(ExpenseCategory::where('place_id', Filament::getTenant()->id)->pluck('name', 'id')),
                //         TextInput::make('sum')->label(__('Sum'))
                //             ->numeric()
                //             ->prefix('₴')
                //             ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                //         Select::make('type_pay')->label(__('Type pay'))
                //             ->default(0)
                //             ->options([
                //                 0 => __('Cash'),
                //                 1 => __('Card')
                //             ])
                //             ->required(),
                //         Textarea::make('comment')->translateLabel()->rows(2),
                // ])
                // ->action(function  (array $data): void {
                //     Expense::create(
                //         [
                //             'place_id' => Filament::getTenant()->id,
                //             'user_id' => Auth::user()->id,
                //             'sum' => $data['sum'],
                //             'added_at' => $data['added_at'],
                //             'comment' => $data['comment'],
                //             'type_pay' => $data['type_pay'],
                //             'pay' => 1
                //         ],
                //     );


                //     Notification::make()
                //     ->title(__('Expense added'))
                //     ->send();
                // }),


            Action::make('openShift')
                ->label(__('Open Shift'))
                ->color('info')
                ->icon('heroicon-o-lock-open')
                ->slideOver()
                ->modalHeading(__('Open Shift'))
                ->requiresConfirmation()
                ->form([
                    Hidden::make('place_id')->default(Filament::getTenant()->id)
                        ->required()
                        ->reactive(),
                ])
                ->action(function (array $data): void {
                    (new PosterApiController())->openShift($data);
                }),


        ];
    }
}
