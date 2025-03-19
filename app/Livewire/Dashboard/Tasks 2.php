<?php

namespace App\Livewire\Dashboard;

use App\Models\Task;
use Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class Tasks extends BaseWidget
{
    protected function getTableHeading(): string|Htmlable
    {
        return __('Tasks');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Task::query()->whereIn('place_id', Auth::user()->places()->pluck('place_id')))

            ->columns([
                TextColumn::make('priority')->translateLabel()->badge(),
                TextColumn::make('date')->translateLabel()
                    ->badge()
                    ->dateTime('d-m-Y')
                    ->description(fn (Task $task): string => $task->time ?: ''),

                TextColumn::make('name')->label(__('Task')),
                TextColumn::make('forUser.name')->label(__('For User')),
            ])
            ->filters([
                Filter::make('opened')->label('Открытые задачи')
                    ->query(fn (Builder $query): Builder => $query->where('approve', false)),
            ]);
    }
}
