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

namespace Hyperf\Lock;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Lock\Driver\DriverInterface;
use Hyperf\Lock\Driver\RedisDriver;
use Hyperf\Lock\Exception\InvalidArgumentException;

use function Hyperf\Support\make;

class LockManager
{
    public function __construct(protected ConfigInterface $config, protected StdoutLoggerInterface $logger, $name = '', $seconds = 0)
    {
    }

    public function getDriver(string $conf = 'default', string $name = '', $seconds = 0): DriverInterface
    {
        $config = $this->config->get("lock.{$conf}");
        if (empty($config)) {
            throw new InvalidArgumentException(sprintf('The lock config %s is invalid.', $conf));
        }

        $driverClass = $config['driver'] ?? RedisDriver::class;
        return make($driverClass, ['config' => $config, 'name' => $name, 'seconds' => $seconds]);
    }
}
