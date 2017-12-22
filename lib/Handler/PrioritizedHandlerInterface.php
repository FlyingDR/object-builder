<?php

namespace Flying\ObjectBuilder\Handler;

/**
 * Interface for object builder handlers that can define their priority
 */
interface PrioritizedHandlerInterface extends HandlerInterface
{
    /**
     * Get priority of this handler
     * Generic handlers should have low priority, more specific handlers should have higher priority
     *
     * @return int
     */
    public function getPriority(): int;
}
