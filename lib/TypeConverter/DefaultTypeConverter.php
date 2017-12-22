<?php

namespace Flying\ObjectBuilder\TypeConverter;

use Flying\ObjectBuilder\PrioritizedHandlerInterface;

/**
 * Default implementation of type converter for object builder
 */
class DefaultTypeConverter implements TypeConverterInterface, PrioritizedHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canConvert(\Reflector $reflection, $value): bool
    {
        if (!($reflection instanceof \ReflectionMethod) || $reflection->getNumberOfParameters() !== 1) {
            return false;
        }

        return \in_array((string)$reflection->getParameters()[0]->getType(), ['bool', 'boolean', 'int', 'integer', 'float', 'double', 'string'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function convert(\Reflector $reflection, &$value): bool
    {
        /** @var \ReflectionMethod $reflection */
        $type = $reflection->getParameters()[0]->getType();
        if ($type === null) {
            // No explicit type is assigned to argument at given method
            return true;
        }
        if ($value === null && $type->allowsNull()) {
            return true;
        }
        switch ((string)$type) {
            case 'bool':
            case 'boolean':
                $value = (boolean)$value;
                return true;
            case 'int':
            case 'integer':
                $value = (int)$value;
                return true;
            case 'float':
            case 'double':
                $value = (float)$value;
                return true;
            case 'string':
                $value = (string)$value;
                return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 0;
    }
}
