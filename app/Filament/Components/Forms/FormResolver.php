<?php

namespace App\Filament\Components\Forms;

use Filament\Forms\Components\Component;

class FormResolver
{
    /**
     * @param  string  $type  The type of form (e.g., 'Supply' or 'Expense').
     * @return array<Component> The array of form components.
     */
    public static function resolve(string $type): array
    {
        // Динамически строим имя класса формы
        // Убедитесь, что 'ExpenseType' в вашем коде возвращает корректные строки,
        // соответствующие именам классов (Supply, Expense и т.д.)
        $class = __NAMESPACE__.'\\'.ucfirst($type).'Form';

        // Проверяем, существует ли класс и содержит ли он метод schema()
        if (class_exists($class) && method_exists($class, 'schema')) {
            // Возвращаем массив компонентов напрямую
            return $class::schema();
        }

        // Возвращаем пустой массив, если класс/схема не найдена
        return [];
    }
}
