<?php

namespace App\Livewire\Dashboard;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Support\Enums\Size;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Seet;
use Auth;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;

class Bookings extends BaseWidget
{
    protected function getTableHeading(): string|Htmlable
    {
        return __('Bookings');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Booking::query()->where('place_id', Filament::getTenant()->id)
                ->whereBetween('time', [Carbon::now(), Carbon::now()->addDays(7)]))

            ->columns([
                TextColumn::make('guest.phone')->label(__('Name'))
                    ->copyable()
                    ->copyMessage(__('Phone copied'))
                    ->copyMessageDuration(1500)
                    ->description(fn (Booking $record): string => $record->guest->fullName ? $record->guest->fullName : $record->guest->name, position: 'above'),
                TextColumn::make('time')->since()->translateLabel()->badge()
                    ->description(fn (Booking $record): string => $record->bookingTime, position: 'above'),
                TextColumn::make('seet.number')->translateLabel()
                    ->badge()->separator(','),
                TextColumn::make('person')->translateLabel(),
            ])->defaultSort('time', 'asc')
            ->recordActions([
                Action::make('complete')->hiddenLabel()
                    ->button()
                    ->icon('heroicon-o-arrow-down')
                    ->visible(fn (Booking $record) => $record['complete'] == 0)
                    ->action(function (Booking $record): void {

                        $record->update(
                            [
                                'complete' => true,
                            ]
                        );
                        $record->save();
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->model(Booking::class)
                    ->createAnother(false)
                    ->modalWidth('sm')
                    ->schema([
                        Hidden::make('user_id')->default(Auth::user()->id),
                        Hidden::make('place_id')->default(Filament::getTenant()->id),
                        Select::make('guest_id')->label(__('Guest'))
                            ->relationship('guest', 'name')
                            ->options(Guest::where('place_id', Filament::getTenant()->id)->get()->pluck('FullName', 'id'))
                            ->searchable(['name', 'comment', 'phone'])
                            ->native(false)
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Hidden::make('place_id')->default(Filament::getTenant()->id),
                                TextInput::make('name')->label(__('Name'))->required(),
                                TextInput::make('phone')->label(__('Phone'))->required()
                                    ->tel()
                                    ->mask(RawJs::make(<<<'JS'
                            $input.length >= 13 ? '(999)999-99-99' : '(999)999-99-99'
                        JS))
                                    ->translateLabel(),
                                TextInput::make('comment')->translateLabel(),
                            ])
                            ->editOptionForm([
                                Hidden::make('place_id')->default(Filament::getTenant()->id),
                                TextInput::make('name')->label(__('Name'))->required(),
                                TextInput::make('phone')->label(__('Phone'))->required()
                                    ->tel()
                                    ->mask(RawJs::make(<<<'JS'
                            $input.length >= 13 ? '(999)999-99-99' : '(999)999-99-99'
                        JS))
                                    ->translateLabel(),
                                TextInput::make('comment')->translateLabel(),
                            ]),
                        Select::make('seet_ids')->label(__('Table'))->options(
                            Seet::get()->pluck('number', 'id')
                        )->multiple(),
                        DateTimePicker::make('time')->label(__('Booking Time'))
                            ->reactive()->required()->native(false)
                            ->seconds(false)
                            ->minDate(now())->default(now()),
                        TextInput::make('person')->label(__('Person'))->numeric()->default(3)

                            ->suffixAction(
                                Action::make('PersonPlus')
                                    ->size(Size::Large)
                                    ->color('success')
                                    ->icon('heroicon-o-user-plus')
                                    ->iconButton()
                                    ->disabled(fn (Get $get): bool => empty($get('person')) || $get('person') >= 20)
                                    ->action(function (Get $get, Set $set, $state): void {
                                        $set('person', $get('person') + 1);
                                    })
                            )
                            ->prefixAction(
                                Action::make('PersonMinus')
                                    ->size(Size::Large)
                                    ->icon('heroicon-o-user-minus')
                                    ->color('danger')
                                    ->iconButton()
                                    ->disabled(fn (Get $get): bool => empty($get('person')) || $get('person') <= 1)
                                    ->action(function (Get $get, Set $set, $state): void {
                                        $set('person', $get('person') - 1);
                                    })
                            ),

                        Textarea::make('comment')->label(__('Comment')),
                    ]),

            ]);
    }
}
