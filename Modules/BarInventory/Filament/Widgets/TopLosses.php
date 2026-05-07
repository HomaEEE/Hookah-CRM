<?php

namespace Modules\BarInventory\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\BarInventory\Models\InventoryLine;
use Modules\BarInventory\Models\InventorySession;
use Modules\BarInventory\Enums\SessionStatus;

class TopLosses extends BaseWidget
{
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $latestSession = InventorySession::where('status', SessionStatus::Completed)
            ->latest('completed_at')
            ->first();

        return $table
            ->query(
                InventoryLine::query()
                    ->where('session_id', $latestSession?->id ?? 0)
                    ->where('variance', '<', 0)
                    ->orderBy('variance', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product'),
                Tables\Columns\TextColumn::make('expected_quantity')
                    ->label('Expected')
                    ->numeric(2),
                Tables\Columns\TextColumn::make('actual_quantity')
                    ->label('Actual')
                    ->numeric(2),
                Tables\Columns\TextColumn::make('variance')
                    ->label('Loss')
                    ->numeric(2)
                    ->color('danger'),
            ])
            ->paginated(false);
    }
}
