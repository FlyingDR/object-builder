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
    public function canConvert(\Reflector $target, array $data, string $key): bool
    {
        if (!($target instanceof \ReflectionMethod) || $target->getNumberOfParameters() !== 1) {
            return false;
        }
        $class = (string)$target->getParameters()[0]->getType();
        return \is_array($data[$key]) && class_exists($class);
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
