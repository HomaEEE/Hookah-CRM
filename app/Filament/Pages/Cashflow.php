<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Cashflow extends Page
{
    use HasFiltersForm;
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string $view = 'filament.pages.cashflow';

    protected static ?int $navigationSort = 2;

    public $startDate;

    public $endDate;

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

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                            ::make('from')
                            ->translateLabel()
                            ->native(false)
                            ->default(now())
                            ->maxDate(fn (Get $get) => $get('to') ?: now()),
                        DatePicker::make('to')
                            ->translateLabel()
                            ->minDate(fn (Get $get) => $get('from') ?: now())
                            ->native(false)
                            ->maxDate(now())
                            ->default(now()),
                    ])
                    ->columns(2),
            ]);
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
