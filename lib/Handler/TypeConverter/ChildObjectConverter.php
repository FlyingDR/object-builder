<?php

namespace Flying\ObjectBuilder\Handler\TypeConverter;

use Flying\ObjectBuilder\Handler\ObjectBuilderAwareHandlerInterface;
use Flying\ObjectBuilder\Handler\PrioritizedHandlerInterface;
use Flying\ObjectBuilder\ObjectBuilderInterface;

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
    public function canConvert(\Reflector $reflection, $value): bool
    {
        if (!($reflection instanceof \ReflectionMethod) || $reflection->getNumberOfParameters() !== 1) {
            return false;
        }
        $class = (string)$reflection->getParameters()[0]->getType();
        return \is_array($value) && class_exists($class);
    }

    /**
     * {@inheritdoc}
     */
    public function convert(\Reflector $reflection, &$value): bool
    {
        /** @var \ReflectionMethod $reflection */
        $class = (string)$reflection->getParameters()[0]->getType();
        try {
            $value = $this->builder->build($class, $value);
            return true;
        } catch (\Exception $e) {
            return false;
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
