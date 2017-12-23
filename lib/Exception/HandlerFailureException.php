<?php

namespace Flying\ObjectBuilder\Exception;

/**
 * Exception to throw in a case if there is some problem with object builder handler
 */
class HandlerFailureException extends ObjectBuilderException
{
    /**
     * {@inheritdoc}
     * @throws HandlerFailureException
     */
    public static function throw(?\Throwable $exception = null, ?string $message = null): void
    {
        throw new self($message ?? 'Object builder handler problem', 0, $exception);
    }
}
