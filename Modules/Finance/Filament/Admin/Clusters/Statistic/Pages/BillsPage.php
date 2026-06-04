<?php

namespace Modules\Finance\Filament\Admin\Clusters\Statistic\Pages;

use App\Http\Controllers\PosterApiController;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Finance\Filament\Admin\Clusters\Statistic\StatisticCluster;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class BillsPage extends Page implements HasTable
{
    use HasPageShield, InteractsWithTable;

    protected string $view = 'filament.clusters.statistic.pages.bills-page';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $cluster = StatisticCluster::class;

    public static function getPluralModelLabel(): string
    {
        return __('Bills');
    }

    public static function getNavigationLabel(): string
    {
        return __('Bills');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Bills');
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => (new PosterApiController)->getBillsWithTableNames(Filament::getTenant()->id))

            ->columns([
                TextColumn::make('transaction_id')->label(__('ID'))
                    ->description(fn (array $record): ?string => $record['table_name']),

                TextColumn::make('date_start')->label(__('Created at'))->badge()
                    ->formatStateUsing(fn ($state) => Carbon::createFromTimestamp($state / 1000, 'Europe/Kyiv')->format('H:i'))
                    ->description(function (array $record): ?string {
                        if (empty($record['date_close'])) {
                            return 'Открыт'; // Текст, если чек еще не закрыт
                        }

                        return Carbon::createFromTimestamp($record['date_close'] / 1000, 'Europe/Kyiv')->format('H:i');
                    }),
                IconColumn::make('print_fiscal')
                    ->label(__(''))
                    ->icon(fn (string $state): Phosphor => match ($state) {
                        '2' => Phosphor::ArrowBendUpLeft,
                        '1' => Phosphor::CheckCircle,
                        '0' => Phosphor::XCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'warning',
                        '1' => 'success',
                        '2' => 'danger',
                        default => 'info',
                    }),
                IconColumn::make('status')
                    ->label(__('Status'))
                    ->icon(fn (string $state): Phosphor => match ($state) {
                        '1' => Phosphor::Plus,
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
                IconColumn::make('pay_type')
                    ->translateLabel(__('Pay type'))
                    ->icon(fn (string $state): Phosphor => match ($state) {
                        '0' => Phosphor::XCircle,
                        '1' => Phosphor::Money,
                        '2' => Phosphor::CreditCard,
                        '3' => Phosphor::Shuffle,

                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'info',
                        '2' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('sum')->money('UAH', divideBy: 100)
                    ->size('xl')
                    ->summarize(Summarizer::make('sum')
                        ->money('UAH')
                        ->using(function (self $livewire): int {
                            return $livewire->getTableRecords()->whereNotIn('status', ['3', '4'])->whereNotIn('pay_type', ['0'])->whereNotIn('print_fiscal', ['2'])->sum('sum') / 100;
                        })
                    )
                    ->translateLabel(),

            ]);
    }
}
