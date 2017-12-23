<?php

namespace Flying\ObjectBuilder\Registry;

use Flying\ObjectBuilder\Exception\HandlerFailureException;
use Flying\ObjectBuilder\Handler\HandlerInterface;
use Flying\ObjectBuilder\Handler\PrioritizedHandlerInterface;

class HandlersList implements HandlersListInterface
{
    /**
     * @var string
     */
    private $interface;
    /**
     * @var HandlerInterface[]
     */
    private $handlers;
    /**
     * @var int
     */
    private $count = 0;
    /**
     * @var int
     */
    private $index = 0;

    /**
     * @param string $interface
     * @param HandlerInterface[] $handlers
     * @throws HandlerFailureException
     */
    public function __construct(string $interface, array $handlers = [])
    {
        $this->interface = $interface;
        $this->set($handlers);
    }

    /**
     * Checks if there is any handlers in list
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return empty($this->handlers);
    }

    /**
     * Checks whether given handler is available into list
     *
     * @param HandlerInterface $handler
     * @return boolean
     */
    public function contains(HandlerInterface $handler): bool
    {
        return \in_array($handler, $this->handlers, true);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return \count($this->handlers);
    }

    /**
     * @param HandlerInterface[] $handlers
     * @return HandlersList
     * @throws HandlerFailureException
     */
    public function set(array $handlers): HandlersList
    {
        $this->handlers = array_map(function ($h) {
            return $this->validate($h);
        }, $handlers);
        $this->update();
        return $this;
    }

    /**
     * Add given handler to the list
     *
     * @param HandlerInterface $handler
     * @return HandlersList
     * @throws HandlerFailureException
     */
    public function add(HandlerInterface $handler): HandlersList
    {
        if (!$this->contains($handler)) {
            $this->handlers[] = $this->validate($handler);
            $this->update();
        }
        return $this;
    }

    /**
     * Removes the specified handler from list
     *
     * @param HandlerInterface $handler
     * @return HandlersList
     */
    public function remove(HandlerInterface $handler): HandlersList
    {
        $this->handlers = array_filter($this->handlers, function ($h) use ($handler) {
            return $h !== $handler;
        });
        $this->update();
        return $this;
    }

    /**
     * Remove all handlers from the list
     *
     * @return HandlersList
     */
    public function clear(): HandlersList
    {
        $this->handlers = [];
        $this->update();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->handlers;
    }

    /**
     * {@inheritdoc}
     * @return HandlerInterface
     */
    public function current(): HandlerInterface
    {
        return $this->handlers[$this->index];
    }

    /**
     * {@inheritdoc}
     * @return int
     */
    public function key(): int
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     * @return void
     */
    public function next(): void
    {
        $this->index++;
    }

    /**
     * {@inheritdoc}
     * @return boolean
     */
    public function valid(): bool
    {
        return $this->index < $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /**
     * @param mixed $handler
     * @return HandlerInterface
     * @throws HandlerFailureException
     */
    protected function validate($handler): HandlerInterface
    {
        if (!\is_object($handler) || !is_subclass_of($handler, $this->interface)) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            throw new HandlerFailureException(sprintf('Object builder handler should implement "%s" interface', (new \ReflectionClass($this->interface))->getShortName()));
        }
        return $handler;
    }

    /**
     * Update handlers list and related parameters after modification
     */
    protected function update()
    {
        usort($this->handlers, function ($a, $b) {
            $ap = $a instanceof PrioritizedHandlerInterface ? $a->getPriority() : 0;
            $bp = $b instanceof PrioritizedHandlerInterface ? $b->getPriority() : 0;
            if ($ap > $bp) {
                return -1;
            }
            if ($ap < $bp) {
                return 1;
            }
            return 0;
        });
        $this->count = $this->count();
        $this->index = 0;
    }
}
