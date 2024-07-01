<?php

declare(strict_types=1);

namespace Hyperf\Lock;

use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CacheAhead;
use Hyperf\Cache\Annotation\CacheEvict;
use Hyperf\Cache\Annotation\CachePut;
use Hyperf\Cache\Annotation\FailCache;
use Hyperf\Cache\Exception\CacheException;
use Hyperf\Cache\Helper\StringHelper;
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

    public function getLockAnnotation(string $className, string $method, array $arguments): Lock
    {
        /** @var Lock $annotation */
        $annotation = $this->getAnnotation(Lock::class, $className, $method);

        return $annotation;
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
