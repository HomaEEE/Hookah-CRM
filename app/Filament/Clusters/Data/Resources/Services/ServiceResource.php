<?php

namespace App\Filament\Clusters\Data\Resources\Services;

use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Clusters\Data\Resources\Services\Pages\ManageServices;
use App\Filament\Clusters\Data\DataCluster;
use App\Filament\Clusters\Data\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $cluster = DataCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 5;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-exclamation-triangle';

    public static function getPluralModelLabel(): string
    {
        return __('Service Contacts');
    }

    // public static function getNavigationGroup(): ?string
    // {
    //     return __('Data');
    // }

    public static function getNavigationLabel(): string
    {
        return __('Service Contacts');
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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('place.city')->translateLabel(),
                TextEntry::make('company')->translateLabel(),
                TextEntry::make('name')->translateLabel(),
                TextEntry::make('phone')->translateLabel()
                    ->icon('heroicon-m-phone')
                    ->iconPosition(IconPosition::Before),
            ]);

    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServices::route('/'),
        ];
    }
}
