<?php

namespace Flying\ObjectBuilder\Tests\Fixtures\TargetProvider;

use Flying\ObjectBuilder\ObjectBuilderAwareInterface;
use Flying\ObjectBuilder\ObjectBuilderInterface;
use Flying\ObjectBuilder\TargetProvider\TargetProviderInterface;

class BuilderAwareTypeProvider implements TargetProviderInterface, ObjectBuilderAwareInterface
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
    public function getTarget(\ReflectionClass $reflection, string $name)
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
