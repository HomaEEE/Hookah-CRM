<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Filament\Forms\Form;


class PersonalInfo extends MyProfileComponent
{
    protected string $view = "livewire.user-data-component";

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public array $data;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
            ])
            ->statePath('data');
    }

}
