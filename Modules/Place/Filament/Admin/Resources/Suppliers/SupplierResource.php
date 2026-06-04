<?php

namespace App\Filament\Clusters\Data\Resources\Suppliers;

use App\Filament\Clusters\Data\DataCluster;
use App\Filament\Clusters\Data\Resources\Suppliers\Pages\ManageSuppliers;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\RawJs;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Place\Models\Supplier;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $cluster = DataCluster::class;

    protected static ?string $recordTitleAttribute = 'company';

    protected static ?int $navigationSort = 3;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function getPluralModelLabel(): string
    {
        return __('Suppliers');
    }

    public static function getNavigationLabel(): string
    {
        return __('Suppliers');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                        2 => __('Tabacco'),
                    ]),
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
                TextColumn::make('type')->label(__('Type'))->badge(),
                TextColumn::make('phone')
                    ->icon('heroicon-o-phone')
                    ->label(__('Phone'))
                    ->copyable()
                    ->searchable()
                    ->copyMessage('Phone copied')
                    ->copyMessageDuration(1500)
                    ->alignLeft(),
                IconColumn::make('visible')
                    ->label(__('Visible'))
                    ->size(IconSize::Medium)
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => ! $record->$name,
                        ]);
                    }),
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
                DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('company')->translateLabel(),
                        TextEntry::make('name')->translateLabel(),

                        TextEntry::make('phone')->translateLabel()
                            ->badge()
                            ->copyable()
                            ->icon('heroicon-m-phone')
                            ->iconPosition(IconPosition::Before),
                        TextEntry::make('email')
                            ->icon('heroicon-m-mail')
                            ->iconPosition(IconPosition::Before),
                    ])->columns(2),
                Section::make()
                    ->schema([
                        RepeatableEntry::make('reqvizites')->hiddenLabel()
                            ->schema([
                                TextEntry::make('own')->label(__('FOP'))->copyable(),
                                TextEntry::make('IBAN')->label(__('IBAN'))
                                    ->formatStateUsing(fn (string $state): string => "UA {$state}")
                                    ->badge()->copyable(),
                                TextEntry::make('INN')->label(__('INN'))->copyable(),
                            ])->columns(3),
                    ])->columns(1),
            ])->columns(1);

    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSuppliers::route('/'),
            //  'reqs' => Pages\ManageSupplierReqizites::route('/{record}/reqs'),

        ];
    }
}
