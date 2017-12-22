<?php

namespace Flying\ObjectBuilder\Handler;

use Flying\ObjectBuilder\ObjectBuilderInterface;

/**
 * Interface for object builder handler that needs reference to builder instance
 */
interface ObjectBuilderAwareHandlerInterface
{
    /**
     * Set instance of object builder
     *
     * @param ObjectBuilderInterface $builder
     */
    public function setBuilder(ObjectBuilderInterface $builder);
}
