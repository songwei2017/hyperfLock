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

interface DriverInterface
{
    public function get(?callable $callback = null);

    public function block(?callable $callback = null);

    public function release(): bool;

    public function getOwner(): string;

    public function getSeconds(): int;

    public function forceRelease(): void;
}
