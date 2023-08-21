<?php

namespace App\Enums;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class Transaction
{
    public static $TYPE = [
        0 => 'Расход',
        1 => 'Доход',
        2 => 'Перевод',
    ];
}
