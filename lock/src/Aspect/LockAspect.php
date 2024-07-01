<?php
namespace Hyperf\Lock\Aspect;

use Hyperf\Cache\AnnotationManager;
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
        // 切面切入后，执行对应的方法会由此来负责
        // $proceedingJoinPoint 为连接点，通过该类的 process() 方法调用原方法并获得结果
        // 在调用前进行某些处理
        $className = $proceedingJoinPoint->className;
        $method = $proceedingJoinPoint->methodName;
        $arguments = $proceedingJoinPoint->arguments['keys'];
        $now = time();
        $collector = AnnotationCollector::get($className);
        var_dump($collector);

        // 获取当前方法反射原型
        /** @var \ReflectionMethod **/
        $reflect = $proceedingJoinPoint->getReflectMethod();
        var_dump($reflect);
        // 获取调用方法时提交的参数
        $arguments = $proceedingJoinPoint->getArguments(); // array
        var_dump($arguments);

        $result = $proceedingJoinPoint->process();

        // 在调用后进行某些处理
        return $result;
    }
}
