<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class PhoneCast implements CastsAttributes
{
    /**
     * Преобразует значение при получении из модели
     */
    public function get($model, string $key, $value, array $attributes): ?string
    {
        if (empty($value)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) !== 12 || ! str_starts_with($digits, '380')) {
            return $value;
        }

        $local = substr($digits, 2); // 0671234567

        return sprintf('(%s) %s-%s-%s',
            substr($local, 0, 3),
            substr($local, 3, 3),
            substr($local, 6, 2),
            substr($local, 8, 2)
        );
    }

    /**
     * Преобразует значение перед сохранением в БД
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if (empty($value)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $digits = '38'.$digits;
        }

        if (! str_starts_with($digits, '380')) {
            $digits = '380'.substr($digits, -9);
        }

        return '+'.$digits;
    }
}
