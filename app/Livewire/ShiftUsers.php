<?php

namespace App\Livewire;

use App\Models\ShiftUser;
use App\Models\Task;
use Carbon\Carbon;
use Auth;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions;
use Filament\Tables\Filters\Filter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ShiftUsers extends BaseWidget
{

    protected function getTableHeading(): string | Htmlable
    {
        return __('Shift Users');
    }

    public function table(Table $table): Table
    {
        return $table
        ->query(ShiftUser::query()->whereDate('created_at', Carbon::today()))

            ->columns([
                TextColumn::make('user.name')->translateLabel(),
                TextColumn::make('wage.name')->translateLabel()
                ->badge()
            ]);
    }

}
