<?php

namespace Flying\ObjectBuilder\Handler\TargetProvider;

use Flying\ObjectBuilder\Handler\HandlerInterface;

/**
 * Interface for object builder handlers that are responsible for resolving targets for assigning values to given object
 *
 * Purpose of the target provider is to find a target (either method or property) within target object,
 * object data with given name can be assigned to
 */
interface TargetProviderInterface extends HandlerInterface
{
    /**
     * Decide if this handler will be able to provide targets for given class
     *
     * @param \ReflectionClass $reflection
     * @return bool
     */
    public function canGetTarget(\ReflectionClass $reflection): bool;

    /**
     * Get reflection of the target for assigning given piece of object data to the object
     * In a case if given data item is not assignable to given class - null should be returned
     *
     * @param \ReflectionClass $reflection
     * @param string $name
     * @return \ReflectionMethod|\ReflectionProperty|null    String result is treated as method or property name
     */
    public function getTarget(\ReflectionClass $reflection, string $name): ?\Reflector;
}
