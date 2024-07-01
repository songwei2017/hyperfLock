<?php
namespace Sw2017\Lock\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Lock extends AbstractAnnotation
{
    public function __construct(public string $name, public int $seconds = 0, public bool $block = false)
    {
    }
}