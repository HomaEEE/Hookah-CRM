<?php

namespace App\Filament\Pages;

use App\Http\Controllers\PosterApiController;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Pages\Dashboard as BasePage;

use App\Models\{Tip, Place};

use Auth;

use Carbon\Carbon;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;

class Dashboard extends BasePage
{

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $slug = 'dashboard';

    protected int | string | array $columnSpan = 'full';

    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 2,
        ];
    }

    public function getContentMaxWidth(): ?string
    {
        return 'full';
    }

    public function getTitle(): string | Htmlable
    {
        return __('Dashboard');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\Dashboard\OpenShift::class,
            \App\Filament\Widgets\Dashboard\TipsShift::class,
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
                ->size('lg')
                ->icon('heroicon-o-banknotes')
                ->modalHeading(__('Add Tips'))
                ->modalWidth('sm')
                ->modalAlignment(Alignment::Start)
                ->form([

                    Hidden::make('place_id')
                        ->label(__('Place'))
                        ->default(Auth::user()->place[0]['id'])
                        ->required(),
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

            Action::make('openShift')
                ->label(__('Open Shift'))
                ->color('info')
                ->size('lg')
                ->icon('heroicon-o-lock-open')

                ->modalHeading(__('Open Shift'))
                ->modalWidth('xs')
                ->requiresConfirmation()
                ->form([
                    Select::make('place_id')->label(__('Place'))
                        ->native(false)
                        ->options(
                            Auth::user()->place->pluck('city', 'id')
                        )->reactive()
                        ->required()
                        ->reactive(),
                ])->slideOver()
                ->action(function (array $data): void {
                    (new PosterApiController())->openShift($data);
                }),

            Action::make('closeShift')
                ->label(__('Close Shift'))

                ->visible(auth()->user()->can('view_any'))

                ->color('danger')
                ->size('lg')
                ->icon('heroicon-o-lock-closed')

                ->modalHeading(__('Close Shift'))
                ->modalWidth('xs')
                ->slideOver()
                ->form([

                    Select::make('place_id')->label(__('Place'))
                        ->native(false)
                        ->options(
                            Auth::user()->place->pluck('city', 'id')
                        )->reactive()
                        ->required()
                        ->reactive(),
                    TextInput::make('shift')
                        ->label(__('Add Sum'))
                        ->numeric()
                        ->required(),
                    TextInput::make('comment')
                        ->label(__('Comment'))
                ])
                ->action(function (array $data): void {
                    (new PosterApiController())->closeShift($data);
                }),

        ];
    }
}
