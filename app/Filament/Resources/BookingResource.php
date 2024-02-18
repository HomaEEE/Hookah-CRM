<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use Illuminate\Database\Eloquent\Model;

use App\Models\{Booking, Place, Seet, Guest};
use Auth;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Get;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         \App\Filament\Widgets\CalendarWidget::class,
    //     ];
    // }

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
                Select::make('guest_id')
                ->relationship('guest', 'name')
                ->options(Guest::where('place_id', Filament::getTenant()->id)->pluck('name', 'id'))
                ->searchable(['name', 'comment'])
                ->native(false)
                ->preload()
                ->required()
                ->createOptionForm([
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
                    TextInput::make('name')->label(__('Name'))->required(),
                    TextInput::make('phone')->label(__('Phone'))->required()
                    ->tel()
                    ->mask(RawJs::make(<<<'JS'
                        $input.length >= 13 ? '(999)999-99-99' : '(999)999-99-99'
                    JS))
                    ->translateLabel(),
                    TextInput::make('comment')->translateLabel(),
                ]),
                Select::make('table_id')->label(__('Table'))
                    ->hidden(fn (Get $get) => $get('place_id') == null)
                    ->options(function (callable $get) {
                        if (null == ($get('place_id'))) {
                            return Seet::all()->pluck('table_id', 'id');
                        } else {
                            return Seet::where('place_id', $get('place_id'))->pluck('table_id', 'id');
                        }
                    }),
                DateTimePicker::make('time')->label(__('Booking Time'))
                    ->reactive()->required()->native(false)
                    ->seconds(false)
                    ->minDate(now())->default(now()),
                TextInput::make('person')->label(__('Person'))->numeric()->default(1),
                Textarea::make('comment')->label(__('Comment')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guest.name')->label(__('Name'))->searchable()
                    ->description(fn (Booking $record): string => $record->comment ? $record->comment : ' ', position: 'below'),
                TextColumn::make('time')->since()->translateLabel()->badge(),
                TextColumn::make('seet.table_id')->translateLabel()->toggleable(),
                TextColumn::make('person')->translateLabel()->toggleable(),
            ])->defaultSort('time', 'desc')
            ->filters([
                Filter::make('today')->translateLabel()
                    ->query(fn (Builder $query): Builder => $query->whereDate('time', Carbon::today())),

            ])
            ->actions([
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
                TextEntry::make('guest.phone')->hiddenLabel()->copyable(),
                TextEntry::make('comment')->hiddenLabel(),

                TextEntry::make('time')->translateLabel()->dateTime('d/m/Y H:m'),
                TextEntry::make('seet.table_id')->translateLabel(),
                TextEntry::make('person')->translateLabel(),
            ])->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBookings::route('/'),
        ];
    }
}
