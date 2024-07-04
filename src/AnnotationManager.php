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
use Hyperf\Di\Annotation\AbstractAnnotation;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Lock\Annotation\Lock;
use Hyperf\Lock\Exception\InvalidArgumentException;

class AnnotationManager
{
    public function __construct(protected ConfigInterface $config, protected StdoutLoggerInterface $logger)
    {
    }

    public function getLockAnnotation(string $className, string $method, array $arguments): array
    {
        /** @var Lock $annotation */
        $annotation = $this->getAnnotation(Lock::class, $className, $method);
        $name = '';
        if ($annotation->arg) {
            foreach ($annotation->arg as $arg) {
                if ($arg && isset($arguments[$arg])) {
                    $name .= ':' . $arg . '_' . $arguments[$arg];
                }
            }
        }

        return [$annotation, $name];
    }

    protected function getAnnotation(string $annotation, string $className, string $method): AbstractAnnotation
    {
        $collector = AnnotationCollector::get($className);
        $result = $collector['_m'][$method][$annotation] ?? null;
        if (! $result instanceof $annotation) {
            throw new InvalidArgumentException(sprintf('Annotation %s in %s:%s not exist.', $annotation, $className, $method));
        }
        return $result;
    }
}
