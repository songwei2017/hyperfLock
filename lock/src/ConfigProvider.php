<?php

declare(strict_types=1);

namespace Sw2017\Lock;

use sw2017\lock\src\Aspect\LockAspect;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'aspects' => [
                LockAspect::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The configuration file of lock.',
                    'source' => __DIR__ . '/../publish/lock.php',
                    'destination' => BASE_PATH . '/config/autoload/lock.php',
                ],
            ],
        ];
    }
}
