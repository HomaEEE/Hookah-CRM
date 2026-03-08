<?php

namespace App\Filament\Pages;

use App\Filament\Components\Forms\TaskBoardForm;
use App\Models\Task;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Relaticle\Flowforge\Board;
use Relaticle\Flowforge\BoardPage;
use Relaticle\Flowforge\Column;
use Relaticle\Flowforge\Components\CardFlex;

class TaskBoard extends BoardPage
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-view-columns';

    protected static ?string $navigationLabel = 'Task Board';

    protected static ?int $navigationSort = 2;

    public static function getPluralModelLabel(): string
    {
        return __('Task Board');
    }

    public static function getNavigationLabel(): string
    {
        return __('Task Board');
    }

    public function getTitle(): string
    {
        return __('Task Board');
    }

    public function board(Board $board): Board
    {
        return $board
            ->query($this->getEloquentQuery())
            ->recordTitleAttribute('title')
            ->columnIdentifier('status')
            ->positionIdentifier('position')
            ->columns([
                Column::make('1')->label(__('To Do'))->color('blue'),
                Column::make('2')->label(__('In Progress'))->color('yellow'),
                Column::make('3')->label(__('Completed'))->color('green'),
            ])
            ->columnIdentifier('status')
            ->positionIdentifier('position')
            // ->searchable(['title', 'text'])

            ->cardSchema(fn (Schema $schema) => $schema->components([

                TextEntry::make('text')
                    ->limit(100)
                    ->hiddenLabel()
                    ->color('gray'),
                CardFlex::make([
                    TextEntry::make('due_date')
                        ->badge()
                        ->hiddenLabel()
                        ->date('j F')
                        ->icon('heroicon-o-calendar'),
                    TextEntry::make('forUser.name')
                        ->badge()
                        ->hiddenLabel()
                        ->icon('heroicon-o-user'),
                ])->wrap()->justify('start'),
            ]))

            ->cardActions([
                ViewAction::make()
                    ->model(Task::class)
                    ->schema([
                        Flex::make([
                            Section::make([
                                TextEntry::make('title')
                                    ->weight(FontWeight::Bold)
                                    ->hiddenLabel(),
                                TextEntry::make('text')->label(__('Description')),
                            ]),
                            Section::make([
                                TextEntry::make('forUser.name')
                                    ->hiddenLabel()
                                    ->icon('heroicon-o-user')
                                    ->copyable(),
                                TextEntry::make('due_date')->label(__('Due Date'))
                                    ->date('j F')
                                    ->icon('heroicon-o-calendar-days'),
                            ])->grow(false),
                        ])->from('md'),

                    ]),
                EditAction::make()
                    ->model(Task::class)
                    ->schema(
                        TaskBoardForm::schema(),
                    ),
                DeleteAction::make()->model(Task::class),
            ])->cardAction('view')
            ->columnActions([
                CreateAction::make()
                    ->label(' ')
                    ->iconButton()->icon('heroicon-o-plus')
                    ->model(Task::class)
                    ->schema(
                        TaskBoardForm::schema(),
                    ),

            ]);
    }

    public function getEloquentQuery(): Builder
    {
        return Task::query();
    }
}
