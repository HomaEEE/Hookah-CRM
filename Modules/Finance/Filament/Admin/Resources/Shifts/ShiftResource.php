<?php

namespace Modules\Finance\Filament\Admin\Resources\Shifts;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Finance\Filament\Admin\Resources\Shifts\Pages\CreateShift;
use Modules\Finance\Filament\Admin\Resources\Shifts\Pages\EditShift;
use Modules\Finance\Filament\Admin\Resources\Shifts\Pages\Expenses;
use Modules\Finance\Filament\Admin\Resources\Shifts\Pages\ListShifts;
use Modules\Finance\Filament\Admin\Resources\Shifts\Pages\Users;
use Modules\Finance\Filament\Admin\Resources\Shifts\Pages\ViewShift;
use Modules\Finance\Filament\Admin\Resources\Shifts\Tables\ShiftInfolist;
use Modules\Finance\Filament\Admin\Resources\Shifts\Tables\ShiftTable;
use Modules\Finance\Models\Shift;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
