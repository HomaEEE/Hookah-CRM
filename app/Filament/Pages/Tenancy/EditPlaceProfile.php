<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditPlaceProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Place profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->translateLabel(),
                TextInput::make('city')->translateLabel()
                // ...
            ]);
    }
}
