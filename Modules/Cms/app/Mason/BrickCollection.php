<?php

namespace Modules\Cms\app\Mason;

class BrickCollection
{
    public static function make(): array
    {
        return [
            Hero::class,
            About::class,
            Menu::class,
            Reserve::class,
            Feedbacks::class,
            MenuHero::class,
        ];
    }
}
