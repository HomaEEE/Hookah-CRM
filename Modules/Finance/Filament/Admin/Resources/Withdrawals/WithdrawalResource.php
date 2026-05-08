<?php

namespace Modules\Finance\Filament\Admin\Resources\Withdrawals;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Finance\Filament\Admin\Resources\Withdrawals\Pages\ManageWithdrawals;
use Modules\Finance\Filament\Admin\Resources\Withdrawals\Schemas\WithdrawalForm;
use Modules\Finance\Filament\Admin\Resources\Withdrawals\Schemas\WithdrawalTable;
use Modules\Finance\Models\Withdrawal;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Withdrawals');
    }

    public static function getNavigationLabel(): string
    {
        return __('Withdrawals');
    }

    public static function form(Schema $schema): Schema
    {
        return WithdrawalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WithdrawalTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWithdrawals::route('/'),
        ];
    }
}
