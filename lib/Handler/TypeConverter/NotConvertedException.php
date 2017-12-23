<?php

namespace Flying\ObjectBuilder\Handler\TypeConverter;

/**
 * Exception to throw in a case if type converter fails to convert given object data value for some reason
 */
class NotConvertedException extends \RuntimeException
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @param \Exception $e
     * @return NotConvertedException
     */
    public static function exceptionOccurs(\Exception $e): NotConvertedException
    {
        $exception =  new self(sprintf('Failed to convert type due to exception "%s": %s', \get_class($e), $e->getMessage()));
        $exception->setException($e);
        return $exception;
    }

    /**
     * @return \Exception
     */
    public function getException(): \Exception
    {
        return $this->exception;
    }

    /**
     * @param \Exception $exception
     */
    public function setException(\Exception $exception): void
    {
        $this->exception = $exception;
    }
}
