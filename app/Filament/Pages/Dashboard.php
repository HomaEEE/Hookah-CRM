<?php

namespace App\Filament\Pages;

use App\Http\Controllers\PosterApiController;
use Illuminate\Contracts\Support\Htmlable;

use App\Models\{Bill, Expense, ExpenseCategory, Supplier, Supply, Tip};

use Carbon\Carbon;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Support\Facades\Auth;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $slug = 'dashboard';

    protected static ?int $navigationSort = 1;

    public function getTitle(): string | Htmlable
    {
        return __('Dashboard');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\Dashboard\TodayShift::class,
            \App\Filament\Widgets\Dashboard\PosterShift::class,
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
            ActionGroup::make([

                CreateAction::make('add_expense')->label(__('Expense'))
                    ->model(Expense::class)
                    ->createAnother(false)
                    ->icon('heroicon-o-document-minus')
                    ->modalHeading(__('Add Expense'))
                    ->modalWidth('sm')
                    ->modalIcon('heroicon-o-document-minus')
                    ->keyBindings(['command+s', 'ctrl+s'])
                    ->form([
                        Hidden::make('user_id')->default(Auth::user()->id),
                        Hidden::make('place_id')->default(Filament::getTenant()->id),
                        DateTimePicker::make('added_at')
                            ->label(__('Date Added'))
                            ->native(false)
                            ->seconds(false)
                            ->maxDate(now())->default(now()),
                        Select::make('category_id')->label(__('Category'))
                            ->searchable()
                            ->required()
                            ->options(ExpenseCategory::where('place_id', Filament::getTenant()->id)->pluck('name', 'id')),
                        TextInput::make('sum')->label(__('Sum'))
                            ->numeric()
                            ->prefix('₴')
                            ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                        SpatieMediaLibraryFileUpload::make('invoice')
                            ->label(__('Invoice'))
                            ->collection('supplies')
                            ->multiple()
                            ->imagePreviewHeight(450)
                            ->imageResizeMode('cover')
                            ->imageEditor()
                            ->optimize('webp')
                            ->resize(80),
                        ToggleButtons::make('bill_id')->label(__('Type pay'))
                            ->options(Bill::get()->pluck('name', 'id'))
                            ->default(0)
                            ->required()
                            ->inline()
                            ->grouped()
                            ->live(),
                        Textarea::make('comment')->translateLabel()->rows(2),
                        Toggle::make('pay')->label(__('Paid'))
                        ->default(true)
                        ->inline(),
                    ])
                    ->successNotification(
                        Notification::make()
                             ->success()
                             ->title(__('Expense added'))
                             ->icon('heroicon-o-document-minus')
                    ),

                CreateAction::make('add_supply')->label(__('Supply'))
                    ->model(Supply::class)
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->modalHeading(__('Add Supply'))
                    ->createAnother(false)
                    ->modalWidth('sm')
                    ->keyBindings(['command+s', 'ctrl+s'])
                    ->modalIcon('heroicon-o-inbox-arrow-down')
                    ->form([
                        Hidden::make('user_id')->default(Auth::user()->id),
                        Hidden::make('place_id')->default(Filament::getTenant()->id),
                        DateTimePicker::make('added_at')
                            ->label(__('Date Added'))
                            ->native(false)
                            ->seconds(false)
                            ->maxDate(now())->default(now()),
                        Select::make('supplier_id')->label(__('Supplier'))
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->options(Supplier::where('place_id', Filament::getTenant()->id)->visible()->pluck('company', 'id')),
                        TextInput::make('sum')->label(__('Sum'))
                            ->numeric()
                            ->required()
                            ->prefix('₴')
                            ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                        SpatieMediaLibraryFileUpload::make('invoice')
                            ->label(__('Invoice'))
                            ->collection('supplies')
                            ->multiple()
                            ->imagePreviewHeight(450)
                            ->imageResizeMode('cover')
                            ->imageEditor()
                            ->optimize('webp')
                            ->resize(80),
                        Textarea::make('comment')->label(__('Comment'))
                            ->rows(3),
                            ToggleButtons::make('bill_id')->label(__('Type pay'))
                            ->options(Bill::get()->pluck('name', 'id'))
                            ->default(0)
                            ->required()
                            ->inline()
                            ->grouped()
                            ->live(),
                        Toggle::make('pay')->label(__('Paid'))
                            ->default(false)
                            ->inline(),
                    ])
                    ->successNotification(
                        Notification::make()
                             ->success()
                             ->icon('heroicon-o-inbox-arrow-down')
                             ->title(__('Supply added'))
                     ),

            ])->button()->label(__('Add'))->icon('heroicon-o-squares-plus'),

            Action::make('addTips')
                ->label(__('Add Tips'))
                ->color('success')
                ->modalWidth('xs')
                ->icon('heroicon-o-banknotes')
                ->modalHeading(__('Add Tips'))
                ->keyBindings(['command+s', 'ctrl+s'])
                ->requiresConfirmation()
                ->form([
                    Hidden::make('place_id')->default(Filament::getTenant()->id),
                    TextInput::make('tips')->label('')
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
                ->icon('heroicon-o-lock-open')
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

        ];
    }
}
