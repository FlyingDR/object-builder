<?php

namespace Flying\ObjectBuilder;

/**
 * Exception to throw in a case if exception was thrown during object building process
 * and object builder runs into debug mode
 */
class DebugException extends \RuntimeException
{
    /**
     * @param \Throwable $exception
     */
    public function __construct(\Throwable $exception)
    {
        parent::__construct('Exception was thrown during building object', 0, $exception);
    }
}
