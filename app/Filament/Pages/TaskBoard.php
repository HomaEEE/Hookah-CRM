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

class TaskBoard extends BoardPage
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-view-columns';

    protected static ?string $navigationLabel = 'Task Board';

    protected static ?string $title = 'Task Board';

    protected static ?int $navigationSort = 10;

    public function board(Board $board): Board
    {
        return $board
            ->query($this->getEloquentQuery())
            ->recordTitleAttribute('title')
            ->columnIdentifier('status')
            ->positionIdentifier('position')
            ->columns([
                Column::make('1')->label('To Do')->color('gray'),
                Column::make('2')->label('In Progress')->color('blue'),
                Column::make('3')->label('Completed')->color('green'),
            ])
            ->columnIdentifier('status')
            ->positionIdentifier('position')
            ->cardSchema(fn (Schema $schema) => $schema->components([

                TextEntry::make('text')
                    ->hiddenLabel()
                    ->limit(50, end: ' ...'),
                Grid::make(2)->schema([
                    TextEntry::make('due_date')
                        ->date()
                        ->icon('heroicon-o-calendar')
                        ->hiddenLabel()
                        ->badge(),
                    TextEntry::make('forUser.name')
                        ->icon('heroicon-o-user')
                        ->hiddenLabel(),
                ]),

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
                                TextEntry::make('text')
                                    ->hiddenLabel(),
                            ]),
                            Section::make([
                                TextEntry::make('forUser.name')
                                    ->hiddenLabel()
                                    ->icon('heroicon-o-user')
                                    ->copyable(),
                                TextEntry::make('due_date')->date()
                                    ->hiddenLabel()
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
