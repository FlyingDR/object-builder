<?php

namespace Flying\ObjectBuilder\Handler\TypeConverter;

use Flying\ObjectBuilder\Handler\ObjectBuilderAwareHandlerInterface;
use Flying\ObjectBuilder\Handler\PrioritizedHandlerInterface;
use Flying\ObjectBuilder\ObjectBuilderInterface;
use Flying\ObjectBuilder\ReflectionCache\ReflectionCache;

/**
 * Converter to transform object assignments passed as arrays into actual objects
 */
class ChildObjectConverter implements TypeConverterInterface, ObjectBuilderAwareHandlerInterface, PrioritizedHandlerInterface
{
    /**
     * @var ObjectBuilderInterface
     */
    private $builder;

    /**
     * {@inheritdoc}
     */
    public function setBuilder(ObjectBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function canConvert(\Reflector $target, array $data, string $key): bool
    {
        if (!($target instanceof \ReflectionMethod) || $target->getNumberOfParameters() !== 1) {
            return false;
        }
        if (!array_key_exists($key, $data) || !\is_array($data[$key])) {
            return false;
        }
        $class = (string)$target->getParameters()[0]->getType();
        try {
            // We need to make sure that class is exists,
            // but class_exists() function returns false on interfaces that are valid for our case
            // so we need to check ability to create reflection for the class
            ReflectionCache::getReflection($class);
            return true;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     * @throws NotConvertedException
     */
    public function convert(\Reflector $target, array $data, string $key)
    {
        /** @var \ReflectionMethod $target */
        $class = (string)$target->getParameters()[0]->getType();
        try {
            return $this->builder->build($class, $data[$key]);
        } catch (\Exception $e) {
            throw NotConvertedException::exceptionOccurs($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 10;
    }
}
