<?php

namespace Flying\ObjectBuilder;

/**
 * Interface for object builder handlers that can define their priority
 */
interface PrioritizedHandlerInterface
{
    /**
     * Get priority of this handler
     * Generic handlers should have low priority, more specific handlers should have higher priority
     *
     * @return int
     */
    public function getPriority(): int;
}
