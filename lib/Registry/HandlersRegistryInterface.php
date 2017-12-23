<?php

namespace Flying\ObjectBuilder\Registry;

use Flying\ObjectBuilder\Handler\HandlerInterface;

/**
 * Interface for object builder handlers registry
 */
interface HandlersRegistryInterface
{
    /**
     * @param HandlerInterface|HandlerInterface[]|HandlersListInterface|HandlersListInterface[]|HandlersRegistryInterface|HandlersRegistryInterface[]|null $handlers
     */
    public function __construct($handlers = null);

    /**
     * Add given object builder handlers to handlers registry
     *
     * @param HandlerInterface|HandlerInterface[]|HandlersListInterface|HandlersListInterface[]|HandlersRegistryInterface|HandlersRegistryInterface[] $handlers
     */
    public function addHandlers($handlers): void;

    /**
     * Get object builder handlers of given or all available types
     *
     * @param string|null $type
     * @return HandlersList|HandlersList[]
     */
    public function getHandlers(?string $type = null);
}
