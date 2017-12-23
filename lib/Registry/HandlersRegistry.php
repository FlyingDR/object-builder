<?php

namespace Flying\ObjectBuilder\Registry;

use Flying\ObjectBuilder\Exception\HandlerFailureException;
use Flying\ObjectBuilder\Handler\DataProcessor\DataProcessorInterface;
use Flying\ObjectBuilder\Handler\HandlerInterface;
use Flying\ObjectBuilder\Handler\ObjectConstructor\ObjectConstructorInterface;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Handler\TypeConverter\TypeConverterInterface;
use Flying\ObjectBuilder\Handler\ValueAssigner\ValueAssignerInterface;

class HandlersRegistry implements HandlersRegistryInterface
{
    protected static $handlerTypes = [
        DataProcessorInterface::class,
        ObjectConstructorInterface::class,
        TargetProviderInterface::class,
        TypeConverterInterface::class,
        ValueAssignerInterface::class,
    ];
    /**
     * @var HandlersListInterface[]
     */
    private $handlers;

    /**
     * {@inheritdoc}
     * @throws HandlerFailureException
     */
    public function __construct($handlers = null)
    {
        $this->handlers = [];
        if ($handlers !== null) {
            $this->addHandlers($handlers);
        }
    }

    /**
     * {@inheritdoc}
     * @throws HandlerFailureException
     */
    public function addHandlers($handlers): void
    {
        if (!\is_array($handlers)) {
            $handlers = [$handlers];
        }
        foreach ((array)$handlers as $handler) {
            if (!\is_object($handler)) {
                throw new HandlerFailureException(sprintf('Only objects are accepted as handlers, "%s" given', \gettype($handler)));
            }
            if ($handler instanceof HandlersRegistryInterface) {
                foreach ($handler->getHandlers() as $list) {
                    $this->addHandlers($list);
                }
                continue;
            }
            if ($handler instanceof HandlersListInterface) {
                $this->addHandlers($handler->toArray());
                continue;
            }

            if (!$handler instanceof HandlerInterface) {
                throw new HandlerFailureException(sprintf('Object builder handler "%s" should implement HandlerInterface', \get_class($handler)));
            }
            $assigned = false;
            foreach (self::$handlerTypes as $type) {
                if (!is_subclass_of($handler, $type)) {
                    continue;
                }
                if (!array_key_exists($type, $this->handlers)) {
                    $this->handlers[$type] = new HandlersList($type);
                }
                $this->handlers[$type]->add($handler);
                $assigned = true;
            }
            if (!$assigned) {
                throw new HandlerFailureException(sprintf('Unknown object builder handler type "%s"', \get_class($handler)));
            }
        }
    }

    /**
     * {@inheritdoc}
     * @throws HandlerFailureException
     */
    public function getHandlers(?string $type = null)
    {
        if ($type === null) {
            return $this->handlers;
        }
        if (!\in_array($type, self::$handlerTypes, true)) {
            throw new HandlerFailureException(sprintf('Unknown object builder handlers type "%s"', $type));
        }
        if (!array_key_exists($type, $this->handlers)) {
            $this->handlers[$type] = new HandlersList($type);
        }
        return $this->handlers[$type];
    }
}
