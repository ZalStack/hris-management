<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'karyawans',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'karyawans',
        ],
    ],

    'providers' => [
        'karyawans' => [
            'driver' => 'eloquent',
            'model' => App\Models\Karyawan::class,
        ],
    ],

    'passwords' => [
        'karyawans' => [
            'provider' => 'karyawans',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];