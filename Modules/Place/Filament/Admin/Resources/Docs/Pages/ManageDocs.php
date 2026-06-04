<?php

namespace Modules\Place\Filament\Admin\Resources\Docs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Place\Filament\Admin\Resources\Docs\DocResource;

class ManageDocs extends ManageRecords
{
    protected static string $resource = DocResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make()->slideOver(),
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

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
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
