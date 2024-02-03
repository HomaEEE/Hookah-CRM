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
            ActionGroup::make([

                Action::make('add_expense')->label(__('Expense'))
                    ->icon('heroicon-o-document-minus')
                    ->modalHeading(__('Add Expense'))
                    ->modalWidth('sm')
                    ->modalIcon('heroicon-o-document-minus')
                    ->form([
                        DateTimePicker::make('added_at')
                            ->label(__('Date Added'))
                            ->native(false)
                            ->maxDate(now())->default(now()),
                        Select::make('category_id')->label(__('Category'))
                            ->searchable()
                            ->required()
                            ->options(ExpenseCategory::where('place_id', Filament::getTenant()->id)->pluck('name', 'id')),
                        TextInput::make('sum')->label(__('Sum'))
                            ->numeric()
                            ->prefix('₴')
                            ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                        Select::make('bill_id')->label(__('Type pay'))
                            ->options(Bill::get()->pluck('name', 'id'))
                            ->required(),
                        Textarea::make('comment')->translateLabel()->rows(2),
                        Toggle::make('pay')->label(__('Paid'))
                        ->default(true)
                        ->inline(),
                    ])
                    ->action(function (array $data): void {
                        Expense::create(
                            [
                                'place_id' => Filament::getTenant()->id,
                                'user_id' => Auth::user()->id,
                                'sum' => $data['sum'],
                                'category_id' => $data['category_id'],
                                'added_at' => $data['added_at'],
                                'comment' => $data['comment'],
                                'bill_id' => $data['bill_id'],
                                'pay' => $data['pay']
                            ],
                        );
                        Notification::make()
                            ->title(__('Expense added'))
                            ->send();
                    }),

                CreateAction::make('add_supply')->label(__('Supply'))
                    ->model(Supply::class)
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->modalHeading(__('Add Supply'))
                    ->modalWidth('sm')
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
                            ->translateLabel()
                            ->collection('supplies')
                            ->multiple()
                            ->imagePreviewHeight(100)
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('2100')
                            ->optimize('webp')
                            ->resize(80),
                        Textarea::make('comment')->label(__('Comment'))
                            ->rows(3),
                        Select::make('bill_id')->label(__('Type pay'))
                            ->default(0)
                            ->options(Bill::get()->pluck('name', 'id'))
                            ->required(),
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
