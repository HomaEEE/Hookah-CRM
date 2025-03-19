<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;

class ShiftUsers extends BaseWidget
{
    protected function getTableHeading(): string|Htmlable
    {
        return __('Shift Users');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->whereDate('updated_at', Carbon::today()))

            ->columns([
                TextColumn::make('name')->translateLabel(),
                TextColumn::make('wage.name')->translateLabel(),
                TextColumn::make('shift_day')->translateLabel()
                    ->badge(),
            ]);
    }
}
