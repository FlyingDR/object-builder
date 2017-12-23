<?php

namespace Flying\ObjectBuilder\Exception;

/**
 * Exception to throw in a case if object build is failed and it is necessary to inform outer code about it
 */
class BuildFailedException extends ObjectBuilderException
{
    /**
     * {@inheritdoc}
     * @throws BuildFailedException
     */
    public static function throw(?\Throwable $exception = null, ?string $message = null): void
    {
        throw new self($message ?? 'Object build is failed', 0, $exception);
    }
}
