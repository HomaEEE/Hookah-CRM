<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;


use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Htmlable;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class DayShift extends Page
{

    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static string $view = 'filament.pages.day-shift';

    protected static ?string $slug = 'shift';

    public static function getPluralModelLabel(): string
    {
        return __('Shift');
    }

    public static function getNavigationLabel(): string
    {
        return __('Shift');
    }

    public function getTitle(): string | Htmlable
    {
        return __('Shift');
    }


    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\Shift\PosterShift::class,
            \App\Filament\Widgets\Shift\ExpensesShift::class,
        ];
    }


}
