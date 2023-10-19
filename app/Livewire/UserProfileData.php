<?php

namespace App\Livewire;

use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use Livewire\Component;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Support\RawJs;

class UserProfileData extends MyProfileComponent
{

    use InteractsWithForms, InteractsWithActions;

    protected string $view = "livewire.user-profile-data";


    public array $data;
    public $user;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                DatePicker::make('birth')->translateLabel()->native(false),

            ])
            ->statePath('data');
    }

    public function submit()
    {
        $data = collect($this->form->getState())->only($this->only)->all();
        $this->user->update($data);
        Notification::make()
            ->success()
            ->title(__('filament-breezy::default.profile.personal_info.notify'))
            ->send();
    }

}
