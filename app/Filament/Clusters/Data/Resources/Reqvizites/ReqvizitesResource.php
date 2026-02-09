<?php

namespace App\Filament\Clusters\Data\Resources\Reqvizites;

use App\Filament\Clusters\Data\DataCluster;
use App\Filament\Clusters\Data\Resources\Reqvizites\Pages\ManageReqvizites;
use App\Models\Reqvizite;
use App\Models\Supplier;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReqvizitesResource extends Resource
{
    protected static ?string $model = Reqvizite::class;

    protected static ?string $cluster = DataCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 4;

    public static function getPluralModelLabel(): string
    {
        return __('Reqvizites');
    }

    public static ?string $navigationParentItem = 'Service Contacts';

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;
    // public static function getNavigationGroup(): ?string
    // {
    //     return __('Data');
    // }

    public static function getNavigationLabel(): string
    {
        return __('Reqvizites');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageReqvizites::route('/'),
        ];
    }
}
