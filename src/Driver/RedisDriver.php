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

use Hyperf\Redis\Redis;
use Psr\Container\ContainerInterface;

class RedisDriver extends Driver
{
    protected Redis $redis;

    protected $name = '';

    public function __construct(ContainerInterface $container, array $config, $name = '', $seconds = 0)
    {
        parent::__construct($container, $config, $name, $seconds);

        $this->redis = $container->get(Redis::class);
    }

    public function acquire(): bool
    {
        if ($this->getSeconds() > 0) {
            return $this->redis->set($this->getKey(), $this->getOwner(), ['NX', 'EX' => $this->getSeconds()]) == true;
        }
        return $this->redis->setNX($this->getKey(), $this->getOwner()) === true;
    }

    public function release(): bool
    {
        return (bool) $this->redis->eval(self::luaReleaseLock(), [$this->getKey(), $this->getOwner()], 1);
    }

    public function getCurrentOwner(): string
    {
        return $this->redis->get($this->getKey());
    }

    public function forceRelease(): void
    {
        $this->redis->del($this->getKey());
    }

    public static function luaReleaseLock(): string
    {
        return <<<'LUA'
if redis.call("get",KEYS[1]) == ARGV[1] then
    return redis.call("del",KEYS[1])
else
    return 0
end
LUA;
    }
}
