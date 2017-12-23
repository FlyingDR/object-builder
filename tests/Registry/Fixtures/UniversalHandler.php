<?php

namespace Flying\ObjectBuilder\Tests\Registry\Fixtures;

use Flying\ObjectBuilder\Handler\DataProcessor\DataProcessorInterface;
use Flying\ObjectBuilder\Handler\ObjectConstructor\ObjectConstructorInterface;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Handler\TypeConverter\TypeConverterInterface;
use Flying\ObjectBuilder\Handler\ValueAssigner\ValueAssignerInterface;

class UniversalHandler implements DataProcessorInterface, ObjectConstructorInterface, TargetProviderInterface, TypeConverterInterface, ValueAssignerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canProcess(\ReflectionClass $reflection, array $data): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function process(\ReflectionClass $reflection, array $data): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function canConstruct(\ReflectionClass $reflection, array $data): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function construct(\ReflectionClass $reflection, array &$data)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function canGetTarget(\ReflectionClass $reflection): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget(\ReflectionClass $reflection, string $name): ?\Reflector
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function canConvert(\Reflector $reflection, array $data, string $key): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(\Reflector $reflection, array $data, string $key)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function canAssign($target, \Reflector $reflection, $value): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function assign($target, \Reflector $reflection, $value): bool
    {
        return false;
    }
}
