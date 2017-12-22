<?php

namespace Flying\ObjectBuilder\ValueAssigner;

/**
 * Interface for value assigners for object builder
 */
interface ValueAssignerInterface
{
    /**
     * Determine if this value assigner is able to assign given value to given target
     *
     * @param object $target
     * @param \Reflector $reflection
     * @param mixed $value
     * @return boolean
     */
    public function canAssign($target, \Reflector $reflection, $value): bool;

    /**
     * Convert given value into given type
     *
     * @param object $target
     * @param \Reflector $reflection
     * @param mixed $value
     * @return boolean
     */
    public function assign($target, \Reflector $reflection, $value): bool;
}
