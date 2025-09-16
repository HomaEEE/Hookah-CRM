<?php

namespace App\Filament\Clusters\Data\Resources;

use App\Filament\Clusters\Data;
use App\Filament\Clusters\Data\Resources\ReqvizitesResource\Pages;
use App\Models\Reqvizite;
use App\Models\Supplier;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReqvizitesResource extends Resource
{
    protected static ?string $model = Reqvizite::class;

    protected static ?string $cluster = Data::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 4;

    public static function getPluralModelLabel(): string
    {
        return __('Reqvizites');
    }

    public static ?string $navigationParentItem = 'Service Contacts';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;
    // public static function getNavigationGroup(): ?string
    // {
    //     return __('Data');
    // }

    public static function getNavigationLabel(): string
    {
        return __('Reqvizites');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supplier_id')->label(__('Supplier'))
                    ->relationship('supplier', 'company')
                    ->searchable()
                    ->searchingMessage(__('Searching supplier...'))
                    ->searchDebounce(500)
                    ->native(false)
                    ->required()
                    ->options(Supplier::where('place_id', Filament::getTenant()->id)->visible()->pluck('company', 'id'))
                    ->createOptionForm([
                        Section::make()
                            ->schema([
                                TextInput::make('name')->label(__('Name')),
                                TextInput::make('phone')->label(__('Phone')),
                                TextInput::make('company')->label(__('Company')),
                                TextInput::make('email')->label(__('Email'))->email(),
                            ])->columns(2),
                    ]),
                TextInput::make('IBAN')->label(__('IBAN'))->prefix('UA'),
                TextInput::make('INN')->label(__('INN'))->numeric(),
                TextInput::make('own')->label(__('FOP')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier.company')->translateLabel()
                    ->description(fn (Reqvizite $record): string => $record->own ?? ''),
                TextColumn::make('IBAN')->label(__('IBAN'))
                    ->description(fn (Reqvizite $record): string => $record->inn ?? ''),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(''),
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReqvizites::route('/'),
        ];
    }
}
