<?php

namespace Flying\ObjectBuilder\TypeConverter;

/**
 * Interface for type converters for object builder
 */
interface TypeConverterInterface
{
    /**
     * Determine if this converter is able to convert given value into given type
     *
     * @param \ReflectionMethod|\ReflectionProperty $reflection
     * @param mixed $value
     * @return boolean
     */
    public function canConvert(\Reflector $reflection, $value): bool;

    /**
     * Convert given value into given type
     *
     * @param \ReflectionMethod|\ReflectionProperty $reflection
     * @param mixed $value Value to convert, passed by reference
     * @return boolean  true if value was converted, false if not
     */
    public function convert(\Reflector $reflection, &$value): bool;
}
