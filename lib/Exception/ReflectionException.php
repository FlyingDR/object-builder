<?php

namespace Flying\ObjectBuilder\Exception;

/**
 * Exception to throw in a case if there is some kind of reflection related exception occurs
 */
class ReflectionException extends ObjectBuilderException
{
    /**
     * {@inheritdoc}
     * @throws ReflectionException
     */
    public static function throw(?\Throwable $exception = null, ?string $message = null): void
    {
        throw new self($message ?? 'Reflection failure', 0, $exception);
    }
}
