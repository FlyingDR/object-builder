<?php

namespace Flying\ObjectBuilder\Handler\ValueAssigner;

use Flying\ObjectBuilder\Handler\PrioritizedHandlerInterface;

/**
 * Default implementation of value assigner for object builder
 */
class DefaultValueAssigner implements ValueAssignerInterface, PrioritizedHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canAssign($object, \Reflector $target, $value): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function assign($object, \Reflector $target, $value): bool
    {
        if ($target instanceof \ReflectionMethod) {
            $target->invoke($object, $value);
            return true;
        }

        if ($target instanceof \ReflectionProperty) {
            if (!$target->isPublic()) {
                $target->setAccessible(true);
            }
            $target->setValue($object, $value);
            if (!$target->isPublic()) {
                $target->setAccessible(false);
            }
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
