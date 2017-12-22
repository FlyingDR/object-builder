<?php

namespace Flying\ObjectBuilder\TargetProvider;

use Flying\ObjectBuilder\PrioritizedHandlerInterface;

/**
 * Default implementation of target provider for object builder
 */
class DefaultTargetProvider implements TargetProviderInterface, PrioritizedHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canGetTarget(\ReflectionClass $reflection): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget(\ReflectionClass $reflection, string $name)
    {
        return 'set' . str_replace(' ', '', ucwords(strtr($name, '_-', '  ')));
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 0;
    }
}
