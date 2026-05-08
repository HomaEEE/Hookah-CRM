<?php

namespace Modules\Finance\Filament\Admin\Resources\Expenses\Pages;

use Closure;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Finance\Filament\Admin\Resources\Expenses\ExpenseResource;

class ManageExpenses extends ManageRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->simplePaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }

    protected function getActions(): array
    {
        return [
            CreateAction::make()->createAnother(false),
        ];
    }

    protected function getTableRecordActionUsing(): ?Closure
    {
        return fn () => 'view';
    }
}
