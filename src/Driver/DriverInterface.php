<?php
namespace  Hyperf\Lock\Driver;
interface DriverInterface
{
    public function get(?callable $callback = null);

    public function block(int $seconds, ?callable $callback = null);

    public function release(): bool;

    public function getOwner(): string;

    public function getSeconds(): int;

    public function forceRelease(): void;
}