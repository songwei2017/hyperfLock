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

namespace Sw2017\Lock\Driver;

use Hyperf\Stringable\Str;
use Sw2017\Lock\Exception\LockTimeoutException;
use Hyperf\Support\Traits\InteractsWithTime;
use Psr\Container\ContainerInterface;

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

    public function __construct(ContainerInterface $container, array $config,$name = '',$seconds = 0)
    {
        $this->container = $container;
        $this->config = $config;
        $this->prefix = $config['prefix'] ?? 'lock:';
        $this->owner = Str::random();
        $this->seconds = $seconds;
        $this->name = $name;
    }


    abstract public function acquire(): bool;

    abstract function release(): bool;


    abstract function forceRelease(): void;

    abstract protected function getCurrentOwner();


    function getKey(): string
    {
        return $this->prefix . $this->name;
    }
    function getOwner(): string
    {
        return $this->owner;
    }
    function getSeconds(): int
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

    public function block(int $seconds, ?callable $callback = null)
    {
        $starting = $this->currentTime();

        while (! $this->acquire()) {
            \swoole\Coroutine::sleep($this->sleepSeconds );
            if ($this->currentTime() - $seconds >= $starting) {
                throw new LockTimeoutException("等待锁超时");
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

}
