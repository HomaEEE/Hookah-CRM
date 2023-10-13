<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuestResource\Pages;
use App\Filament\Resources\GuestResource\RelationManagers;
use App\Models\Guest;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GuestResource extends Resource
{
    protected static ?string $model = Guest::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'name';


    public static function getPluralModelLabel(): string
    {
        return __('Guests');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Data');
    }

    public static function getNavigationLabel(): string
    {
        return __('Guests');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                TextInput::make('name')->label(__('Name')),
                TextInput::make('phone')->label(__('Phone'))
                    ->tel()
                    ->mask(RawJs::make(<<<'JS'
                        $input.length >= 13 ? '(999)999-99-99' : '(999)999-99-99'
                    JS))
                    ->translateLabel(),
                TextInput::make('comment')->translateLabel(),
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            TextEntry::make('name')->translateLabel(),
            TextEntry::make('phone')->translateLabel(),
            TextEntry::make('comment')->translateLabel(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon('heroicon-o-users')
            ->columns([
                TextColumn::make('name')->label(__('Name'))
                    ->searchable()
                    ->alignLeft()
                    ->description(fn (Guest $record): string => $record->comment ?? '', position: 'below'),
                TextColumn::make('phone')
                    ->icon('heroicon-o-phone')
                    ->label(__('Phone'))
                    ->copyable()
                    ->searchable()
                    ->copyMessage(__('Phone copied'))
                    ->copyMessageDuration(1500)
                    ->alignLeft(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGuests::route('/'),
        ];
    }
}
