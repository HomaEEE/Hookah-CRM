<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\IconPosition;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $recordTitleAttribute = 'name';


    public static function getPluralModelLabel(): string
    {
        return __('Service Contacts');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Data');
    }

    public static function getNavigationLabel(): string
    {
        return __('Service Contacts');
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
                TextInput::make('company')->label(__('Company')),
                TextInput::make('comment')->translateLabel(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('Name'))
                    ->searchable()
                    ->alignLeft()
                    ->description(fn (Service $record): string => $record->company ?? '', position: 'below'),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            TextEntry::make('place.city')->translateLabel(),
            TextEntry::make('company')->translateLabel(),
            TextEntry::make('name')->translateLabel(),
            TextEntry::make('phone')->translateLabel()
            ->icon('heroicon-m-phone')
            ->iconPosition(IconPosition ::Before),
        ]);

    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageServices::route('/'),
        ];
    }
}
