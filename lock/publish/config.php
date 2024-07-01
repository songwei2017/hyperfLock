<?php
return [
    'default' => [
        'driver' => Sw2017\Lock\Driver\RedisDriver::class,
        'timeout' => 10 * 1000000,
        'prefix' => 'lock:',
    ],
];