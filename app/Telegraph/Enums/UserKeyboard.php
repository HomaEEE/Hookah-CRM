<?php

namespace App\Telegraph\Enums;

class UserKeyboard
{
    public const MAIN_ADMIN = [
        [
            ['text' => 'Menu'],
            ['text' => 'Order'],
        ],
        [
            ['text' => 'booking'],
            ['text' => 'Settings'],
        ],
        //        [
        //            ['text' => 'Admin panel', 'web_app' => ['url' => 'https://payme.uz']]
        //        ]
    ];

    const MAIN_MENU_ARRAY = [
        'Menu' => 'sendMessage',
        'Order' => 'getStats',
        'booking' => 'resetToken',
        'Settings' => 'addChannel',
    ];
}
