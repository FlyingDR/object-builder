<?php

namespace Flying\ObjectBuilder;

/**
 * Interface for object builders
 */
interface ObjectBuilderInterface
{
    /**
     * Type: boolean
     * Default: false
     *
     * true to throw exception in a case if given data can't be completely assigned to the object being built
     */
    public const STRICT = 'strict';
    /**
     * Type: boolean
     * Default: false
     *
     * true to re-throw all exceptions that occurs during object building process
     */
    public const DEBUG = 'debug';

    /**
     * Build object of given class name and assign given data to it
     *
     * @param string $class
     * @param array $data
     * @param array $options
     * @return object
     */
    public function build(string $class, array $data = [], array $options = []);
}
