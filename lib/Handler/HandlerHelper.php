<?php

namespace Flying\ObjectBuilder\Handler;

use Flying\ObjectBuilder\Exception\ReflectionException;

class HandlerHelper
{
    /**
     * Determine if given reflection object belongs to the given class
     * Useful for defining usage scopes of the object builder handlers
     * if they need to work only for some classes sub-tree
     *
     * @param \Reflector $reflection
     * @param string $class
     * @return bool
     * @throws ReflectionException
     */
    public static function ifClass(\Reflector $reflection, string $class): bool
    {
        if (method_exists($reflection, 'getDeclaringClass')) {
            $reflection = $reflection->getDeclaringClass();
        }
        if ($reflection instanceof \ReflectionClass) {
            return $reflection->name === $class || $reflection->isSubclassOf($class);
        }
        throw new ReflectionException(sprintf('Failed to determine class name by reflection "%s"', \get_class($reflection)));
    }
}
