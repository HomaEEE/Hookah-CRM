<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use App\Models\Expense;
use Auth;
use Closure;
use Illuminate\Support\Str;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;

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
