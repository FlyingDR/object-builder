<?php

namespace Flying\ObjectBuilder\Exception;

/**
 * Exception to throw in a case if target provider decides
 * that given piece of object data should be skipped and not assigned to object
 * Useful for example of object data contains information that is indirectly required
 * for object building process but should not be assigned to the object itself
 */
class SkipDataException extends ObjectBuilderException
{
    /**
     * {@inheritdoc}
     * @throws SkipDataException
     */
    public static function throw(?\Throwable $exception = null, ?string $message = null): void
    {
        throw new self($message ?? 'This data item should be skipped', 0, $exception);
    }
}
