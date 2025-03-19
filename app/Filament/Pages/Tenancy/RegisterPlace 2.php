<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Place;
use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterPlace extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register place';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),

            ]);
    }

    protected function handleRegistration(array $data): Place
    {
        $place = Place::create($data);
        $place->users()->attach(auth()->user());
        return $place;
    }
}
