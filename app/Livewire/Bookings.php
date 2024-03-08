<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Seet;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use GuzzleHttp\Promise\Create;
use Illuminate\Contracts\Support\Htmlable;
use Auth;

class Bookings extends BaseWidget
{

    protected function getTableHeading(): string | Htmlable
    {
        return __('Bookings');
    }

    public function table(Table $table): Table
    {
        return $table
        ->query(Booking::query()->where('place_id', Filament::getTenant()->id)
        ->whereBetween('time', [Carbon::now(), Carbon::now()->addDays(7)]))

            ->columns([
                TextColumn::make('guest.name')->label(__('Name'))
                ->description(fn (Booking $record): string => $record->guest->comment ? $record->guest->comment : $record->guest->phone, position: 'below'),
                TextColumn::make('time')->since()->translateLabel()
                ->description(fn (Booking $record): string => $record->bookingTime, position: 'below'),
                TextColumn::make('table')->translateLabel()->badge(),
                TextColumn::make('guest.phone')->label(__('Phone'))
                ->copyable()
                    ->copyMessage(__('Phone copied'))
                    ->copyMessageDuration(1500),
                TextColumn::make('person')->translateLabel(),
            ])->defaultSort('time', 'asc')
            ->actions([
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
                })
            ])
            ->headerActions([
                CreateAction::make()
                ->model(Booking::class)
                ->modalWidth('sm')
                ->form([
                    Hidden::make('user_id')->default(Auth::user()->id),
                    Hidden::make('place_id')->default(Filament::getTenant()->id),
                    Select::make('guest_id')
                    ->relationship('guest', 'name')
                    ->options(Guest::where('place_id', Filament::getTenant()->id)->pluck('name', 'id'))
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
                    Select::make('table')->label(__('Table'))->options(
                        Seet::where('place_id', Filament::getTenant()->id)->pluck('table_id', 'id')
                    )->multiple(),
                    DateTimePicker::make('time')->label(__('Booking Time'))
                        ->reactive()->required()->native(false)
                        ->seconds(false)
                        ->minDate(now())->default(now()),
                    TextInput::make('person')->label(__('Person'))->numeric()->default(1),
                    Textarea::make('comment')->label(__('Comment')),
                ])

            ]);
    }

}
