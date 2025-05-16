<?php

namespace App\Filament\Clusters\Dev\Pages;

use App\Filament\Clusters\Dev;
use App\Models\Task;
use App\Models\User;
use Auth;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Relaticle\Flowforge\Filament\Pages\KanbanBoardPage;

class TasksBoard extends KanbanBoardPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    public static function getNavigationLabel(): string
    {
        return __('Tasks');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Tasks');
    }

    protected static ?string $title = 'Tasks';

    protected static ?string $cluster = Dev::class;

    public function getSubject(): Builder
    {
        return Task::query();
    }

    public function mount(): void
    {

        $this
            ->titleField('title')
            ->orderField('sort_order')
            ->columnField('status')
            ->descriptionField('text')

            ->columns([
                '0' => __('To Do'),
                '1' => __('In Progress'),
                '2' => __('Completed'),
            ])
            ->columnColors([
                '1' => 'blue',
                '2' => 'yellow',
                '3' => 'green',
            ])

            ->cardAttributes([
                //   'created_at' => __('Date'),
                'date' => __('Date'),
                'forUser.name' => __('For User'),
                // 'priority' => __('Priority'),
            ])
            ->cardAttributeColors([
                'created_at' => 'blue',
                'date' => 'red',
                'forUser.name' => 'yellow',
            ]);
    }

    public function createAction(Action $action): Action
    {
        return $action
            ->iconButton()
            ->icon('heroicon-o-plus')
            ->modalHeading('Create Task')
            ->modalWidth('xl')
            ->form(function (Forms\Form $form) {
                return $form->schema([
                    Hidden::make('user_id')->default(Auth::user()->id),
                    Hidden::make('place_id')->label(__('Place'))
                        ->default(Filament::getTenant()->id),
                    Select::make('for_user_id')->label(__('For User'))
                        ->hidden(fn (Get $get) => $get('place_id') == null)
                        ->options(function (callable $get) {
                            return User::whereHas('place', function ($query) {
                                $query->where('place_id', Filament::getTenant()->id);
                            })->get()
                                ->pluck('name', 'id');
                        })
                        ->allowHtml()
                        ->searchable(),
                    DateTimePicker::make('date')->translateLabel()
                        ->native(false)
                        ->seconds(false),
                    TextInput::make('title')->label(__('Task')),
                    Textarea::make('text')->label(__('Task text'))
                        ->autosize(),
                ]);
            });
    }

    public function editAction(Action $action): Action
    {
        return $action
            ->modalHeading('Edit Task')
            ->modalWidth('xl')
            ->form(function (Forms\Form $form) {
                return $form->schema([
                    Select::make('for_user_id')->label(__('For User'))
                        ->hidden(fn (Get $get) => $get('place_id') == null)
                        ->options(function (callable $get) {
                            return User::whereHas('place', function ($query) {
                                $query->where('place_id', Filament::getTenant()->id);
                            })->get()->pluck('name', 'id');
                        })
                        ->searchable(),
                    DateTimePicker::make('date')->translateLabel()
                        ->native(false)
                        ->seconds(false),
                    TextInput::make('title')->label(__('Task')),
                    Textarea::make('text')->label(__('Task text'))
                        ->autosize(),
                ]);
            });
    }
}
