<?php

namespace Flying\ObjectBuilder\Registry;

/**
 * Interface for object builder handlers lists
 */
interface HandlersListInterface extends \Countable, \Iterator
{
    /**
     * Get list of handlers as associative array
     *
     * @return array
     */
    public function toArray(): array;
}
