<?php

namespace Flying\ObjectBuilder\Handler\TargetProvider;

use Flying\ObjectBuilder\Handler\PrioritizedHandlerInterface;
use Flying\ObjectBuilder\ReflectionCache\ReflectionCache;

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
    public function getTarget(\ReflectionClass $reflection, string $name): ?\Reflector
    {
        $method = 'set' . str_replace(' ', '', ucwords(strtr($name, '_-', '  ')));
        $methods = ReflectionCache::getMethods($reflection);
        return $methods[$method] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 0;
    }
}
