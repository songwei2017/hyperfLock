<?php
namespace Hyperf\Lock\Aspect;

use Hyperf\Lock\AnnotationManager;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Lock\Annotation\Lock;
use Hyperf\Lock\LockManager;

class LockAspect extends AbstractAspect
{

    public function __construct(protected LockManager $manager, protected AnnotationManager $annotationManager)
    {
    }


    // 要切入的类或 Trait，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public array $classes = [];

    // 要切入的注解，具体切入的还是使用了这些注解的类，仅可切入类注解和类方法注解
    public array $annotations = [
        Lock::class,
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $className = $proceedingJoinPoint->className;
        $method = $proceedingJoinPoint->methodName;
        $arguments = $proceedingJoinPoint->arguments['keys'];
        $now = time();

        $lockAnnotation = $this->annotationManager->getLockAnnotation($className, $method, $arguments);

        $driver = $this->manager->getDriver( $lockAnnotation->conf,$lockAnnotation->name,$lockAnnotation->seconds);

        $result = $driver->{$lockAnnotation->method}(function ()use ($proceedingJoinPoint){
            return $proceedingJoinPoint->process();
        });
        return $result;
    }
}
