<?php

namespace Modules\Finance\Filament\Admin\Clusters\Statistic\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Finance\Filament\Admin\Clusters\Statistic\StatisticCluster;

class Cashflow extends Page
{
    use HasFiltersForm, HasPageShield, InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $cluster = StatisticCluster::class;

    protected string $view = 'filament.pages.cashflow';

    protected static ?int $navigationSort = 2;

    public $startDate;

    public $endDate;

    public function mount(): void
    {

        // Инициализация фильтров
        $this->filtersForm->fill([
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
        ]);
    }

    public function persistsFiltersInSession(): bool
    {
        return false;
    }

    public static function getPluralModelLabel(): string
    {
        return __('Cashflow');
    }

    public static function getNavigationLabel(): string
    {
        return __('Cashflow');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Cashflow');
    }

    public $reports = null;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        DatePicker::make('from')
                            ->translateLabel()
                            ->native(false)
                            ->default(now()->startOfMonth())
                            ->placeholder(now()->startOfMonth())
                            ->defaultFocusedDate(now()->startOfMonth())->closeOnDateSelection()
                            ->maxDate(fn (Get $get) => $get('to') ?: now())
                            ->live(),
                        DatePicker::make('to')
                            ->translateLabel()
                            ->minDate(fn (Get $get) => $get('from') ?: now())
                            ->native(false)
                            ->closeOnDateSelection()
                            ->default(now())
                            ->placeholder(now()->endOfMonth())
                            ->defaultFocusedDate(now())
                            ->maxDate(now())->live(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ])
            ->statePath('filters');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(function () {
                    $start = $this->filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
                    $end = $this->filters['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

                    return route('cashflow.report.pdf', [
                        'start' => $start,
                        'end' => $end,
                    ]);
                })
                ->openUrlInNewTab(),
        ];

    }
}
