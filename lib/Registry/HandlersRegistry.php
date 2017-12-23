<?php

namespace Flying\ObjectBuilder\Registry;

use Flying\ObjectBuilder\Handler\HandlerInterface;
use Flying\ObjectBuilder\Handler\ObjectConstructor\ObjectConstructorInterface;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Handler\TypeConverter\TypeConverterInterface;
use Flying\ObjectBuilder\Handler\ValueAssigner\ValueAssignerInterface;

class HandlersRegistry implements HandlersRegistryInterface
{
    protected static $handlerTypes = [
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
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
     */
    public function addHandlers($handlers): void
    {
        if (!\is_array($handlers)) {
            $handlers = [$handlers];
        }
        foreach ((array)$handlers as $handler) {
            if (!\is_object($handler)) {
                throw new \InvalidArgumentException(sprintf('Only objects are accepted as handlers, "%s" given', \gettype($handler)));
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
                throw new \InvalidArgumentException(sprintf('Object builder handler "%s" should implement HandlerInterface', \get_class($handler)));
            }
            $type = array_reduce(self::$handlerTypes, function ($result, $type) use ($handler) {
                return is_subclass_of($handler, $type) ? $type : $result;
            });
            if (!$type) {
                throw new \InvalidArgumentException(sprintf('Unknown object builder handler type "%s"', \get_class($handler)));
            }
            if (!array_key_exists($type, $this->handlers)) {
                $this->handlers[$type] = new HandlersList($type);
            }
            $this->handlers[$type]->add($handler);
        }
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function getHandlers(?string $type = null)
    {
        if ($type === null) {
            return $this->handlers;
        }
        if (!\in_array($type, self::$handlerTypes, true)) {
            throw new \InvalidArgumentException(sprintf('Unknown object builder handlers type "%s"', $type));
        }
        if (!array_key_exists($type, $this->handlers)) {
            $this->handlers[$type] = new HandlersList($type);
        }
        return $this->handlers[$type];
    }
}
