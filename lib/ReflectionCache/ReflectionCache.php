<?php

namespace Flying\ObjectBuilder\ReflectionCache;

use Flying\ObjectBuilder\Exception\ReflectionException;

class ReflectionCache implements ReflectionCacheInterface
{
    /**
     * @var \ReflectionClass[]
     */
    private static $reflections = [];
    /**
     * @var \ReflectionMethod[][]
     */
    private static $methods = [];
    /**
     * @var \ReflectionProperty[][]
     */
    private static $properties = [];

    /**
     * {@inheritdoc}
     * @throws ReflectionException
     */
    public static function getReflection(string $class): \ReflectionClass
    {
        if (!array_key_exists($class, self::$reflections)) {
            try {
                self::$reflections[$class] = new \ReflectionClass($class);
            } /** @noinspection PhpRedundantCatchClauseInspection */ catch (\ReflectionException $e) {
                ReflectionException::throw($e, sprintf('Failed to create reflection for class "%s", it probably doesn\'t exists', $class));
            }
        }
        return self::$reflections[$class];
    }

    /**
     * {@inheritdoc}
     */
    public static function getMethods(\ReflectionClass $reflection): array
    {
        $class = $reflection->name;
        if (!array_key_exists($class, self::$methods)) {
            // Create list of methods that we have in this class
            $methods = [];
            do {
                foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    if (!array_key_exists($method->getName(), $methods) && !$method->isAbstract()) {
                        $methods[$method->getName()] = $method;
                    }
                }
                $reflection = $reflection->getParentClass();
            } while ($reflection);
            self::$methods[$class] = $methods;
        }
        return self::$methods[$class];
    }

    /**
     * {@inheritdoc}
     */
    public static function getProperties(\ReflectionClass $reflection): array
    {
        $class = $reflection->name;
        if (!array_key_exists($class, self::$properties)) {
            // Create list of properties that we have in this class
            $properties = [];
            do {
                foreach ($reflection->getProperties() as $property) {
                    if (!array_key_exists($property->getName(), $properties)) {
                        $properties[$property->getName()] = $property;
                    }
                }
                $reflection = $reflection->getParentClass();
            } while ($reflection);
            self::$properties[$class] = $properties;
        }
        return self::$properties[$class];
    }
}
