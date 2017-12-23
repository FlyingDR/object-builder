<?php

namespace Flying\ObjectBuilder\Handler\ValueAssigner;

use Flying\ObjectBuilder\Handler\HandlerInterface;

/**
 * Interface for value assigners for object builder
 *
 * Purpose of the value assigner is to assign given value to given target within given object
 */
interface ValueAssignerInterface extends HandlerInterface
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
