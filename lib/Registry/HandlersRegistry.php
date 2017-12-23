<?php

namespace Flying\ObjectBuilder\Registry;

use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Handler\TypeConverter\TypeConverterInterface;
use Flying\ObjectBuilder\Handler\ValueAssigner\ValueAssignerInterface;

class HandlersRegistry implements HandlersRegistryInterface
{
    protected static $handlerTypes = [
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
        foreach (self::$handlerTypes as $type) {
            $this->handlers[$type] = new HandlersList($type);
        }
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
            $assigned = false;
            foreach (self::$handlerTypes as $type) {
                if (is_subclass_of($handler, $type)) {
                    $this->handlers[$type]->add($handler);
                    $assigned = true;
                    break;
                }
            }
            if (!$assigned) {
                throw new \InvalidArgumentException(sprintf('Unknown object builder handlers type "%s"', (new \ReflectionObject($handler))->getShortName()));
            }
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
        if (array_key_exists($type, $this->handlers)) {
            return $this->handlers[$type];
        }
        throw new \InvalidArgumentException(sprintf('Unknown object builder handlers type "%s"', $type));
    }
}
