<?php

namespace App\Enums;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class Meter
{
    public static $TYPE = [
        0 => 'Электроэнергия',
        1 => 'Вода',
        2 => 'Газ',
    ];
}
