<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Sw2017\Lock\Driver\RedisDriver;

return [
    'default' => [
        'driver' => RedisDriver::class,
        'timeout' => 10 * 1000000,
        'prefix' => 'lock:',
    ],
];
