<?php

namespace App\Filament\Clusters\Dev\Pages;

use App\Filament\Clusters\Dev\DevCluster;
use App\Http\Controllers\PosterApiController;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class BillsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.clusters.dev.pages.bills-page';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $cluster = DevCluster::class;

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => (new PosterApiController)->getAllBills(Filament::getTenant()->id))

            ->columns([
                 IconColumn::make('print_fiscal')
                    ->label(__(''))
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        '1' => Heroicon::CheckCircle,
                        '0' => Heroicon::XCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'warning',
                        '1' => 'success',
                        '2' => 'danger',
                        default => 'info',
                    }),
                TextColumn::make('transaction_id')->label(__('ID')),
                TextColumn::make('table_id')->label(__('Table')),



                IconColumn::make('status')
                    ->label(__('Status'))
                    ->icon(fn (string $state): Phosphor => match ($state) {
                        '1' => Phosphor::PlusSquare,
                        '2' => Phosphor::FlagBannerFold,
                        '3' => Phosphor::TrashSimple,
                        '4' => Phosphor::Truck,

                    })
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'info',
                        '2' => 'success',
                        '3' => 'danger',
                        '4' => 'success',

                    }),

                TextColumn::make('date_start')->label(__('Created at'))
                    ->formatStateUsing(fn ($state) => Carbon::createFromTimestamp($state / 1000, 'Europe/Kyiv')->format('d-m H:i')),
                TextColumn::make('date_close')
                    ->formatStateUsing(fn ($state) => Carbon::createFromTimestamp($state / 1000, 'Europe/Kyiv')->format('d.m H:i')),

                IconColumn::make('pay_type')
                    ->translateLabel(__('Pay type'))
                    ->icon(fn (string $state): Phosphor => match ($state) {
                        '0' => Phosphor::XCircle,
                        '1' => Phosphor::Money,
                        '2' => Phosphor::CreditCard,
                        '3' => Phosphor::Shuffle,

                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'warning',
                        '1' => 'info',
                        '2' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('sum')->money('UAH', divideBy: 100)
                    ->summarize(Summarizer::make('sum')
                        ->money()
                        ->using(function (self $livewire): int {
                            return $livewire->getTableRecords()->where('pay_type', '!=', '0')->whereNotIn('status', ['3', '4'])->sum('sum') / 100;
                        })
                    )
                    ->translateLabel(),

            ]);
    }
}
