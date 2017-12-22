<?php

namespace Flying\ObjectBuilder;

/**
 * Interface to inform object builder that object needs reference to builder instance
 */
interface ObjectBuilderAwareInterface
{
    /**
     * Set instance of object builder
     *
     * @param ObjectBuilderInterface $builder
     */
    public function setBuilder(ObjectBuilderInterface $builder);
}
