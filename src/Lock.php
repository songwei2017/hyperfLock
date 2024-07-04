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

use Hyperf\Lock\Driver\DriverInterface;

class Lock
{
    protected DriverInterface $driver;

    protected string $name;

    public function __construct(LockManager $manager, string $name = '', int $seconds = 0)
    {
        $this->driver = $manager->getDriver('default', $name, $seconds);
    }

    public function __call($name, $arguments)
    {
        return $this->driver->{$name}(...$arguments);
    }

    public function get(?callable $callback = null): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function block($key): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
