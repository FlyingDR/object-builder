<?php

namespace Flying\ObjectBuilder\ValueAssigner;

use Flying\ObjectBuilder\PrioritizedHandlerInterface;

/**
 * Default implementation of value assigner for object builder
 */
class DefaultValueAssigner implements ValueAssignerInterface, PrioritizedHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canAssign($target, \Reflector $reflection, $value): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function assign($target, \Reflector $reflection, $value): bool
    {
        if ($reflection instanceof \ReflectionMethod) {
            $reflection->invoke($target, $value);
            return true;
        }

        if ($reflection instanceof \ReflectionProperty) {
            $reflection->setValue($target, $value);
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 0;
    }
}
