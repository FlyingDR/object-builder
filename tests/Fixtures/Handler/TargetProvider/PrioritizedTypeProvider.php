<?php

namespace Flying\ObjectBuilder\Tests\Fixtures\Handler\TargetProvider;

use Flying\ObjectBuilder\Handler\PrioritizedHandlerInterface;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;

class PrioritizedTypeProvider implements TargetProviderInterface, PrioritizedHandlerInterface
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
    public function getPriority(): int
    {
        return 0;
    }
}
