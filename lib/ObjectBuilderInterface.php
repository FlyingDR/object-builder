<?php

namespace Flying\ObjectBuilder;

/**
 * Interface for object builders
 */
interface ObjectBuilderInterface
{
    /**
     * Build object of given class name and assign given data to it
     *
     * @param string $class
     * @param array $data
     * @param bool $strict true to throw exception in a case if given data can't be completely assigned to the object being built
     * @return object
     */
    public function build(string $class, array $data = [], $strict = false);
}
