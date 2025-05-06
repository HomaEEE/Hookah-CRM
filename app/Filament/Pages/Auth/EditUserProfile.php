<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Support\RawJs;
use Illuminate\Support\Collection;

class EditUserProfile extends BaseEditProfile
{
    public Collection $wage;

    // protected static string $view = 'filament.pages.profile';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                TextInput::make('phone')
                    ->tel()
                    ->translateLabel()
                    ->mask(RawJs::make(<<<'JS'
                                $input.length >= 13 ? '(999)999-99-99' : '(999)999-99-99'
                            JS)),
                TextInput::make('creditcard')
                    ->translateLabel()
                    ->mask(RawJs::make(<<<'JS'
                    $input.length >= 16 ? '9999-9999-9999-9999' : '9999-9999-9999-9999'
                JS)),
                DatePicker::make('birth')->translateLabel()
                    ->native(false),
            ]);
    }
}
