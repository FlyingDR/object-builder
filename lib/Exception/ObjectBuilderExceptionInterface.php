<?php

namespace Flying\ObjectBuilder\Exception;

/**
 * Interface for exception classes for object builder and its handlers
 */
interface ObjectBuilderExceptionInterface
{
    /**
     * @param \Throwable|null $exception
     * @param string|null $message
     */
    public static function throw(?\Throwable $exception = null, ?string $message = null): void;
}
