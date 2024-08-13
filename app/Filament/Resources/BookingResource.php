<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use Illuminate\Database\Eloquent\Model;

use App\Models\{Booking, Seet, Guest};
use Auth;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\{Get, Set};
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action as FormAction;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('Place');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Bookings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Bookings');
    }

    protected function getTableReorderColumn(): ?string
    {
        return 'time';
    }

    public static function getNavigationBadge(): ?string
    {
        return Booking::query()->whereDate('time', '>=', Carbon::today())->count();
    }

    protected function getTableQuery(): Builder
    {

        return Booking::query();
    }

    public static function form(Form $form): Form
    {
        return $form
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
                    Seet::where('place_id', Filament::getTenant()->id)->pluck('number', 'id')
                )->multiple(),
                DateTimePicker::make('time')->label(__('Booking Time'))
                    ->reactive()->required()->native(false)
                    ->seconds(false)
                    ->minDate(now())->default(now()),
                TextInput::make('person')->label(__('Person'))->numeric()->default(3)

                    ->suffixAction(
                        FormAction::make('PersonPlus')
                            ->size(ActionSize::Large)
                            ->color('success')
                            ->icon('heroicon-o-user-plus')
                            ->iconButton()
                            ->disabled(fn (Get $get): bool => empty($get('person')) || $get('person') >= 20)
                            ->action(function (Get $get, Set $set, $state): void {
                                $set('person', $get('person') + 1);
                            })
                    )
                    ->prefixAction(
                        FormAction::make('PersonMinus')
                            ->size(ActionSize::Large)
                            ->icon('heroicon-o-user-minus')
                            ->color('danger')
                            ->iconButton()
                            ->disabled(fn (Get $get): bool => empty($get('person')) || $get('person') <= 1)
                            ->action(function (Get $get, Set $set, $state): void {
                                $set('person', $get('person') - 1);
                            })
                    ),
                Textarea::make('comment')->label(__('Comment')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guest.name')->label(__('Name'))->searchable()
                    ->description(fn (Booking $record): string => $record->guest->comment ? $record->guest->comment : '', position: 'below'),
                TextColumn::make('guest.phone')->label(__('Phone'))->searchable()
                    ->copyable()
                    ->copyMessage(__('Phone copied'))
                    ->copyMessageDuration(1500),
                TextColumn::make('time')->since()->translateLabel()->badge()
                    ->description(fn (Booking $record): string => $record->bookingTime, position: 'below'),
                TextColumn::make('seet.number')->translateLabel()
                    ->badge()->separator(','),
                TextColumn::make('person')->translateLabel()->toggleable(),
            ])->defaultSort('time', 'desc')
            ->filters([
                Filter::make('today')->translateLabel()
                    ->query(fn (Builder $query): Builder => $query->whereDate('time', Carbon::today())),
            ])
            ->actions([
                Tables\Actions\ReplicateAction::make(),
                // ->form([
                //     DateTimePicker::make('time')->seconds(false)
                //     ->native(false)
                //     ->minDate(now())->default(now())
                //     ->translateLabel()
                // ])->beforeSave(function (ReplicateAction $action, Event $record, array  $data) {
                //     $record['time'] = $data['time'];
                //     // Access and apply form data (e.g., $record->custom_field = $data['custom_field'])
                // }),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('guest.name')->hiddenLabel(),
                TextEntry::make('comment')->hiddenLabel(),
                TextEntry::make('guest.phone')->hiddenLabel()->copyable(),
                TextEntry::make('time')->translateLabel()->dateTime('l d/m, H:i'),
                TextEntry::make('table')->translateLabel()->badge(),
                TextEntry::make('person')->translateLabel(),
                TextEntry::make('user.name')->translateLabel(),
            ])->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBookings::route('/'),
        ];
    }
}
