<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Cashflow extends Page
{
    //  use HasFiltersForm;
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string $view = 'filament.pages.cashflow';

    protected static ?int $navigationSort = 2;

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

    // public function filtersForm(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Section::make()
    //                 ->schema([
    //                     Select::make('businessCustomersOnly')
    //                         ->boolean(),
    //                     DatePicker::make('startDate')
    //                         ->maxDate(fn (Get $get) => $get('endDate') ?: now()),
    //                     DatePicker::make('endDate')
    //                         ->minDate(fn (Get $get) => $get('startDate') ?: now())
    //                         ->maxDate(now()),
    //                 ])
    //                 ->columns(3),
    //         ]);
    // }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\Statistic\Bills::make(),
            //  \App\Filament\Widgets\Test\UserShift::make(),
            //  \App\Filament\Widgets\Statistic\GraphProfit::make(),
        ];
    }
}
