<?php

namespace App\Filament\Resources\Bookings;

use Filament\Schemas\Schema;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Bookings\Pages\ManageBookings;
use App\Filament\Components\BookingForm;
use App\Filament\Resources\BookingResource\Pages;
use Illuminate\Database\Eloquent\Model;

use App\Models\{Booking};
use Auth;
use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

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

    // public static function getNavigationBadge(): ?string
    // {
    //     return Booking::query()->whereDate('time', '>=', Carbon::today())->count();
    // }

    protected function getTableQuery(): Builder
    {

        return Booking::query()->whereDate('time', '>=', Carbon::today());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                BookingForm::schema()
            );
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
            ->recordActions([
                ReplicateAction::make(),
                // ->form([
                //     DateTimePicker::make('time')->seconds(false)
                //     ->native(false)
                //     ->minDate(now())->default(now())
                //     ->translateLabel()
                // ])->beforeSave(function (ReplicateAction $action, Event $record, array  $data) {
                //     $record['time'] = $data['time'];
                //     // Access and apply form data (e.g., $record->custom_field = $data['custom_field'])
                // }),
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            'index' => ManageBookings::route('/'),
        ];
    }
}
