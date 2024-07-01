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

namespace Sw2017\Lock;

use Sw2017\Lock\Driver\DriverInterface;
use Sw2017\Lock\Driver\RedisDriver;
use sw2017\lock\src\Exception\InvalidArgumentException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use function Hyperf\Support\make;

class LockManager
{
    public function __construct(protected ConfigInterface $config, protected StdoutLoggerInterface $logger,$name = "",$seconds = 0)
    {
    }

    public function getDriver(string $conf = 'default',string $name = '',$seconds = 0): DriverInterface
    {
      //  var_dump($this->drivers);
     /*   if (isset($this->drivers[$name]) && $this->drivers[$name] instanceof DriverInterface) {
            return $this->drivers[$name];
        }*/

        $config = $this->config->get("lock.{$conf}");
        if (empty($config)) {
            throw new InvalidArgumentException(sprintf('The lock config %s is invalid.', $conf));
        }

        $driverClass = $config['driver'] ?? RedisDriver::class;
        return  make($driverClass, ['config'=>$config,'name'=>$name,'seconds'=>$seconds]);
       // var_dump($driver);
      //  return $this->drivers[$name] = $driver;
    }
}
