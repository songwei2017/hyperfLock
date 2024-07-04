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

namespace Hyperf\Lock\Driver;

use Hyperf\Lock\Exception\LockTimeoutException;
use Hyperf\Stringable\Str;
use Hyperf\Support\Traits\InteractsWithTime;
use Psr\Container\ContainerInterface;
use swoole\Coroutine;

abstract class Driver implements DriverInterface
{
    use InteractsWithTime;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $prefix;

    protected $owner;

    protected $seconds;

    protected $sleepSeconds = 0.01;

    public function __construct(ContainerInterface $container, array $config, $name = '', $seconds = 0)
    {
        $this->container = $container;
        $this->config = $config;
        $this->prefix = $config['prefix'] ?? 'lock:';
        $this->owner = Str::random();
        $this->seconds = $seconds;
        $this->name = $name;
    }

    abstract public function acquire(): bool;

    abstract public function release(): bool;

    abstract public function forceRelease(): void;

    public function getKey(): string
    {
        return $this->prefix . $this->name;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getSeconds(): int
    {
        return $this->seconds;
    }

    public function get(?callable $callback = null)
    {
        $result = $this->acquire();
        if ($result && is_callable($callback)) {
            try {
                return $callback();
            } finally {
                $this->release();
            }
        }
        return $result;
    }

    public function block(?callable $callback = null)
    {
        $starting = $this->currentTime();
        $seconds = $this->getSeconds();
        while (! $this->acquire()) {
            Coroutine::sleep($this->sleepSeconds);
            if ($this->currentTime() - $seconds >= $starting) {
                throw new LockTimeoutException('等待锁超时');
            }
        }

        if (is_callable($callback)) {
            try {
                return $callback();
            } finally {
                $this->release();
            }
        }

        return true;
    }

    abstract protected function getCurrentOwner();
}
