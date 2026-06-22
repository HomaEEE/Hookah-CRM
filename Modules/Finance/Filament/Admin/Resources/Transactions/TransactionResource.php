<?php

namespace Modules\Finance\Filament\Admin\Resources\Transactions;

use App\Enums\NavigationGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Finance\Filament\Admin\Resources\Transactions\Pages\ManageTransactions;
use Modules\Finance\Filament\Admin\Resources\Transactions\Schemas\TransactionForm;
use Modules\Finance\Filament\Admin\Resources\Transactions\Schemas\TransactionInfolist;
use Modules\Finance\Filament\Admin\Resources\Transactions\Tables\TransactionTable;
use Modules\Finance\Models\Transaction;
use Override;
use UnitEnum;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Finance;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getPluralModelLabel(): string
    {
        return __('Transactions');
    }

    public static function getNavigationLabel(): string
    {
        return __('Transactions');
    }

    public static function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionTable::configure($table);
    }

    #[Override]
    public static function infolist(Schema $schema): Schema
    {
        return TransactionInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTransactions::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
