<?php

namespace Flying\ObjectBuilder\Tests\Fixtures\TargetProvider;

use Flying\ObjectBuilder\PrioritizedHandlerInterface;
use Flying\ObjectBuilder\TargetProvider\TargetProviderInterface;

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
    public function getTarget(\ReflectionClass $reflection, string $name)
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
