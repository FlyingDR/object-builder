<?php

namespace Flying\ObjectBuilder\Handler\TypeConverter;

use Flying\ObjectBuilder\Handler\HandlerInterface;

/**
 * Interface for type converters for object builder
 *
 * Purpose of the type converter handler is to apply any required transformations
 * to object data entry before it will be assigned to the target
 */
interface TypeConverterInterface extends HandlerInterface
{
    /**
     * Determine if this converter is able to convert given value into given type
     *
     * @param \ReflectionMethod|\ReflectionProperty $reflection
     * @param array $data
     * @param string $key
     * @return boolean
     */
    public function canConvert(\Reflector $reflection, array $data, string $key): bool;

    /**
     * Convert given value into given type
     *
     * @param \ReflectionMethod|\ReflectionProperty $reflection
     * @param array $data
     * @param string $key
     * @return mixed
     * @throws NotConvertedException
     */
    public function convert(\Reflector $reflection, array $data, string $key);
}
