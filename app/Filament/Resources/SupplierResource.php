<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Support\RawJs;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $recordTitleAttribute = 'company';

    public static function getPluralModelLabel(): string
    {
        return __('Suppliers');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Data');
    }

    public static function getNavigationLabel(): string
    {
        return __('Suppliers');
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
                TextInput::make('mail')->label(__('Email'))->email(),
                TextInput::make('comment')->translateLabel(),
                Toggle::make('visible')->translateLabel()->default(1),
                Select::make('type')->label(__('Type'))
                ->options([
                    0 => __('Products'),
                    1 => __('Alcohol'),
                    2 => __('Tabacco')
                ]),
                KeyValue::make('data')
                    ->keyLabel(__('Rekvizites'))
                    ->valueLabel(__('Value')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('Name'))
                    ->searchable()
                    ->alignLeft()
                    ->description(fn (Supplier $record): string => $record->company, position: 'below'),
                    SelectColumn::make('type')->label(__('Type'))
                    ->options([
                        0 => __('Products'),
                        1 => __('Alcohol'),
                        2 => __('Tabacco')
                    ]),
                    TextColumn::make('phone')
                    ->icon('heroicon-o-phone')
                    ->label(__('Phone'))
                    ->copyable()
                    ->searchable()
                    ->copyMessage('Phone copied')
                    ->copyMessageDuration(1500)
                    ->alignLeft(),
                IconColumn::make('visible')
                    ->label(__('Visible menu'))
                    ->size(IconColumn\IconColumnSize::Medium)
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                     ->action(function ($record, $column) {
                         $name = $column->getName();
                         $record->update([
                             $name => ! $record->$name,
                         ]);
                     }),
                TextColumn::make('data'),
            ])
            ->filters([
                //
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

            TextEntry::make('place.city'),
            TextEntry::make('company'),
            TextEntry::make('name'),

            TextEntry::make('phone')
            ->icon('heroicon-m-phone')
            ->iconPosition(IconPosition::Before),
            Section::make()
                ->schema([
                    RepeatableEntry::make('data')
                    ->schema([
                        TextEntry::make('key'),
                        TextEntry::make('value')
                    ])
                    ->columns(2)
                ])
        ]);

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSuppliers::route('/'),
        ];
    }
}
