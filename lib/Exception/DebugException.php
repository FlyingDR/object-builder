<?php

namespace Flying\ObjectBuilder\Exception;

/**
 * Exception to throw in a case if exception was thrown during object building process
 * and object builder runs into debug mode
 */
class DebugException extends ObjectBuilderException
{
    /**
     * {@inheritdoc}
     * @throws DebugException
     */
    public static function throw(\Throwable $exception, ?string $message = null): void
    {
        throw new self($message ?? 'Exception was thrown during building object', 0, $exception);
    }
}
