<?php

namespace App\Filament\Clusters\Data\Resources\DocResource\Pages;

use App\Filament\Clusters\Data\Resources\DocResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageDocs extends ManageRecords
{
    protected static string $resource = DocResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->slideOver(),
        ];
    }

    public function getTabs(): array
    {
        return [
            __('all') => Tab::make(),
            __('Pass') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 1)),
            __('License') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 2)),
            __('EDR') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 3)),
            __('Key') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 4)),
            __('contracts') => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 5)),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()
                ->columns(3)
                ->schema([
                    // TextEntry::make('supplier.company')->hiddenLabel(),
                    // TextEntry::make('IBAN')->hiddenLabel(),
                    // TextEntry::make('INN')->hiddenLabel(),
                    // TextEntry::make('own')->hiddenLabel(),
                ]),
        ]);
    }
}
