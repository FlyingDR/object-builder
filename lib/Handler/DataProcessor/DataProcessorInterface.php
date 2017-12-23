<?php

namespace Flying\ObjectBuilder\Handler\DataProcessor;

use Flying\ObjectBuilder\Handler\HandlerInterface;

/**
 * Interface for object data processors for object builder
 *
 * Purpose of the data processor is to pre-process given data
 * before it will be passed to further steps of object building process
 */
interface DataProcessorInterface extends HandlerInterface
{
    /**
     * Determine if this handler can pre-process object data for given object
     *
     * @param \ReflectionClass $reflection
     * @param array $data
     * @return bool
     */
    public function canProcess(\ReflectionClass $reflection, array $data): bool;

    /**
     * Apply processing to object data
     *
     * @param \ReflectionClass $reflection
     * @param array $data
     * @return array
     */
    public function process(\ReflectionClass $reflection, array $data): array;
}
