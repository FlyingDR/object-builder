<?php

namespace Flying\ObjectBuilder\Handler\ObjectConstructor;

use Flying\ObjectBuilder\Handler\HandlerInterface;

/**
 * Interface for object builder handlers that are responsible for constructing object instances
 *
 * Purpose of object constructor is to instantiate object by given reflection and object data
 * Object data is passed by reference to allow its modification to e.g. exclude data entries
 * that was used for object construction itself and should not be processed further
 */
interface ObjectConstructorInterface extends HandlerInterface
{
    /**
     * Determine if this handler can construct an instance of object by given information
     *
     * @param \ReflectionClass $reflection
     * @param array $data
     * @return bool
     */
    public function canConstruct(\ReflectionClass $reflection, array $data): bool;

    /**
     * Construct object instance
     *
     * @param \ReflectionClass $reflection
     * @param array $data
     * @return object
     */
    public function construct(\ReflectionClass $reflection, array &$data);
}
