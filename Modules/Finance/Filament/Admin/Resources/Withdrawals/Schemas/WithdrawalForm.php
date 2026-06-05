<?php

namespace Modules\Finance\Filament\Admin\Resources\Withdrawals\Schemas;

use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;

class WithdrawalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::user()->id),
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                DatePicker::make('created_at')->label(__('Date'))->default(now())->required()->native(false)->timezone('Europe/Kyiv'),
                TextInput::make('sum')->label(__('Sum'))->numeric()->prefix('₴')->default(3000),
                ToggleButtons::make('bill_id')->label(__('Type pay'))
                    ->options(BillType::class)->default(0)
                    ->required()
                    ->inline()
                    ->extraAttributes(['style' => 'width: 100%'])
                    ->grouped()
                    ->live(),
            ])->columns(1);
    }
}
