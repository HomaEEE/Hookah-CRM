<?php

namespace App\Livewire;

use Filament\Forms;
use App\Models\User;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class UserProfileData extends MyProfileComponent
{

    protected string $view = "livewire.user-profile-data";

    public array $only = ['creditcard', 'birth', 'phone'];


    protected function getProfileFormSchema()
    {
        $name = Forms\Components\TextInput::make('phone')
        ->tel()
        ->translateLabel()
        ->mask(RawJs::make(<<<'JS'
                        $input.length >= 13 ? '(999)999-99-99' : '(999)999-99-99'
                    JS));
        $email = Forms\Components\TextInput::make('creditcard')
        ->translateLabel()
        ->mask(RawJs::make(<<<'JS'
        $input.length >= 16 ? '9999-9999-9999-9999' : '9999-9999-9999-9999'
     JS));
        $phone = Forms\Components\DatePicker::make('birth')->translateLabel()->native(false);

        return [
            Forms\Components\Group::make([
                $name,
                $email,
                $phone
            ])->columnSpan(3)
        ];
    }

}
