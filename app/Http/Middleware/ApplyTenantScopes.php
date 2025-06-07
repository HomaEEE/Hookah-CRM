<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class ApplyTenantScopes
{
    // Модели, к которым НЕ применяется скоуп
    protected array $excludedModels = [
        \App\Models\User::class,
        \App\Models\Place::class,
        \App\Models\Role::class,
        \App\Models\Permission::class,
        \App\Models\ShiftUser::class,
        // Добавьте другие модели для исключения
    ];

    public function handle(Request $request, Closure $next)
    {
        // Получаем все файлы из директории Models
        $modelFiles = File::allFiles(app_path('Models'));

        foreach ($modelFiles as $file) {
            // Формируем полное имя класса
            $class = 'App\\Models\\'.pathinfo($file->getFilename(), PATHINFO_FILENAME);

            // Проверяем, существует ли класс и является ли он моделью
            if (
                class_exists($class) &&
                is_subclass_of($class, \Illuminate\Database\Eloquent\Model::class) &&
                ! in_array($class, $this->excludedModels)
            ) {
                // Проверяем, использует ли модель трейт SoftDeletes
                $reflection = new ReflectionClass($class);
                $isSoftDeletable = $reflection->hasMethod('bootSoftDeletes');

                // Применяем глобальный скоуп
                $class::addGlobalScope(function (Builder $query) use ($isSoftDeletable) {
                    $query->whereBelongsTo(Filament::getTenant());

                    // Если модель использует SoftDeletes, учитываем только неудаленные записи
                    if ($isSoftDeletable) {
                        $query->whereNull($query->getModel()->getQualifiedDeletedAtColumn());
                    }
                });
            }
        }

        return $next($request);
    }
}
