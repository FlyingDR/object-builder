<?php

namespace Flying\ObjectBuilder\ReflectionCache;

/**
 * Interface for cache of reflection information about objects being built by object builders
 */
interface ReflectionCacheInterface
{
    /**
     * Get class reflection for given class name
     *
     * @param string $class
     * @return \ReflectionClass
     */
    public static function getReflection(string $class): \ReflectionClass;

    /**
     * Get list of method reflections for given class reflection
     *
     * @param \ReflectionClass $reflection
     * @return \ReflectionMethod[]
     */
    public static function getMethods(\ReflectionClass $reflection): array;

    /**
     * Get list of property reflections for given class reflection
     *
     * @param \ReflectionClass $reflection
     * @return \ReflectionProperty[]
     */
    public static function getProperties(\ReflectionClass $reflection): array;
}
