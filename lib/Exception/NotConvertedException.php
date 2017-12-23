<?php

namespace Flying\ObjectBuilder\Exception;

/**
 * Exception to throw in a case if type converter fails to convert given object data value for some reason
 */
class NotConvertedException extends ObjectBuilderException
{
    /**
     * {@inheritdoc}
     * @throws NotConvertedException
     */
    public static function throw(?\Throwable $exception = null, ?string $message = null): void
    {
        throw new self($message ?? 'Failed to convert type due to exception', 0, $exception);
    }
}
