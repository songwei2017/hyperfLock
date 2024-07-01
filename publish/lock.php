<?php
return [
    'default' => [
        'driver' => Hyperf\Lock\Driver\RedisDriver::class,
        'timeout' => 10 * 1000000,
        'prefix' => 'lock:',
    ],
];