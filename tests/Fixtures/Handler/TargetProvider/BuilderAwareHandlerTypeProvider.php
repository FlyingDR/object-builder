<?php

namespace Flying\ObjectBuilder\Tests\Fixtures\Handler\TargetProvider;

use Flying\ObjectBuilder\Handler\ObjectBuilderAwareHandlerInterface;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\ObjectBuilderInterface;

class BuilderAwareHandlerTypeProvider implements TargetProviderInterface, ObjectBuilderAwareHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canGetTarget(\ReflectionClass $reflection): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget(\ReflectionClass $reflection, string $name): ?\Reflector
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setBuilder(ObjectBuilderInterface $builder)
    {

    }
}
