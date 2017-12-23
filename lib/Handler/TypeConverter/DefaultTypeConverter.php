<?php

namespace Flying\ObjectBuilder\Handler\TypeConverter;

use Flying\ObjectBuilder\Handler\PrioritizedHandlerInterface;

/**
 * Default implementation of type converter for object builder
 */
class DefaultTypeConverter implements TypeConverterInterface, PrioritizedHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canConvert(\Reflector $reflection, array $data, string $key): bool
    {
        if (!($reflection instanceof \ReflectionMethod) || $reflection->getNumberOfParameters() !== 1) {
            return false;
        }

        return \in_array((string)$reflection->getParameters()[0]->getType(), [null, 'bool', 'boolean', 'int', 'integer', 'float', 'double', 'string'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function convert(\Reflector $reflection, array $data, string $key)
    {
        /** @var \ReflectionMethod $reflection */
        $type = $reflection->getParameters()[0]->getType();
        $value = $data[$key];
        if ($type === null) {
            // No explicit type is assigned to argument at given method
            return $value;
        }
        if ($value === null && $type->allowsNull()) {
            return $value;
        }
        switch ((string)$type) {
            case 'bool':
            case 'boolean':
                return (boolean)$value;
            case 'int':
            case 'integer':
                return (int)$value;
            case 'float':
            case 'double':
                return (float)$value;
            case 'string':
                return (string)$value;
        }
        throw new NotConvertedException(sprintf('Unsupported data type "%s"', \gettype($value)));
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 0;
    }
}
