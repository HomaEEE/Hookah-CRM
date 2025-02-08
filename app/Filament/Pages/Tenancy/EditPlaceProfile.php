<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Filament\Pages\Tenancy\EditTenantProfile;
use SolutionForest\FilamentTranslateField\Forms\Component\Translate;
use Filament\Resources\Concerns\Translatable;

class EditPlaceProfile extends EditTenantProfile
{

    public static function getLabel(): string
    {
        return __('Place profile');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Translate::make()
                    ->schema([
                        TextInput::make('name')->translateLabel(),
                        TextInput::make('city')->translateLabel(),
                        TextInput::make('address')->label(__('Address')),
                    ])->columnSpan(['md' => 1]),
                    Section::make()
                    ->schema([
                        TextInput::make('phone')->label(__('Phone')),
                        TextInput::make('instagram')->label(__('Instagram')),
                        TextInput::make('slug')->label(__('Sufix for place')),
                        Toggle::make('opened')->inline(),
                    ])->columnSpan(['md' => 1]),
                Section::make()
                    ->schema([
                        KeyValue::make('time')->label(__('Working time'))
                            ->keyLabel(__('Day'))
                            ->valueLabel(__('Time')),
                        KeyValue::make('settings')->label(__('Settings')),
                        KeyValue::make('properties')->label(__('Properties')),
                        KeyValue::make('pays')->label(__('Pays')),
                    ])->columnSpan(['lg' => 3]),
            ]);
    }
}
