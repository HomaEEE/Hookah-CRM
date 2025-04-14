<?php

namespace App\Filament\Pages;

use App\Models\Task;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;

class TaskBoard extends KanbanBoard
{
    protected static string $model = Task::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Dev');
    }

    protected static string $statusEnum = ModelStatus::class;
}
