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

namespace Hyperf\Lock\Aspect;

use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Lock\Annotation\Lock;
use Hyperf\Lock\AnnotationManager;
use Hyperf\Lock\LockManager;

class LockAspect extends AbstractAspect
{
    // 要切入的类或 Trait，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public array $classes = [];

    // 要切入的注解，具体切入的还是使用了这些注解的类，仅可切入类注解和类方法注解
    public array $annotations = [
        Lock::class,
    ];

    public function __construct(protected LockManager $manager, protected AnnotationManager $annotationManager)
    {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $className = $proceedingJoinPoint->className;
        $method = $proceedingJoinPoint->methodName;
        $arguments = $proceedingJoinPoint->arguments['keys'];

        [$lockAnnotation,$name] = $this->annotationManager->getLockAnnotation($className, $method, $arguments);
        $driver = $this->manager->getDriver($lockAnnotation->conf, $lockAnnotation->name . $name, $lockAnnotation->seconds);

        return $driver->{$lockAnnotation->method}(function () use ($proceedingJoinPoint) {
            return $proceedingJoinPoint->process();
        });
    }
}
