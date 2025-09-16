<?php

namespace App\Filament\Resources\Shifts;

use App\Filament\Resources\Shifts\Pages\CreateShift;
use App\Filament\Resources\Shifts\Pages\EditShift;
use App\Filament\Resources\Shifts\Pages\Expenses;
use App\Filament\Resources\Shifts\Pages\ListShifts;
use App\Filament\Resources\Shifts\Pages\Users;
use App\Filament\Resources\Shifts\Pages\ViewShift;
use App\Filament\Resources\Shifts\Tables\ShiftInfolist;
use App\Filament\Resources\Shifts\Tables\ShiftTable;
use App\Models\Shift;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 1;

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Shifts');
    }

    public static function getNavigationLabel(): string
    {
        return __('Shifts');
    }

    protected function getTableQuery(): Builder
    {
        return Shift::query()->fastPaginate();
    }

    public static function table(Table $table): Table
    {

        return ShiftTable::configure($table);

    }

    public static function infolist(Schema $schema): Schema
    {
        return ShiftInfolist::configure($schema);

    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewShift::class,
            Users::class,
            Expenses::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShifts::route('/'),
            'create' => CreateShift::route('/create'),
            'view' => ViewShift::route('/{record}/view'),
            'edit' => EditShift::route('/{record}/edit'),
            'expenses' => Expenses::route('/{record}/expenses'),
            'users' => Users::route('/{record}/users'),
        ];
    }
}
