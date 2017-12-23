<?php

namespace Flying\ObjectBuilder;

use Flying\ObjectBuilder\Handler\DataProcessor\DataProcessorInterface;
use Flying\ObjectBuilder\Handler\HandlerInterface;
use Flying\ObjectBuilder\Handler\ObjectBuilderAwareHandlerInterface;
use Flying\ObjectBuilder\Handler\ObjectConstructor\ObjectConstructorInterface;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Handler\TypeConverter\NotConvertedException;
use Flying\ObjectBuilder\Handler\TypeConverter\TypeConverterInterface;
use Flying\ObjectBuilder\Handler\ValueAssigner\ValueAssignerInterface;
use Flying\ObjectBuilder\ReflectionCache\ReflectionCache;
use Flying\ObjectBuilder\Registry\HandlersRegistryInterface;

class ObjectBuilder implements ObjectBuilderInterface
{
    /**
     * @var array
     */
    private $targetsCache = [];
    /**
     * @var HandlersRegistryInterface
     */
    private $handlers;

    /**
     * @param HandlersRegistryInterface $handlers
     */
    public function __construct(HandlersRegistryInterface $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function build(string $class, array $data = [], $strict = false)
    {
        $reflection = ReflectionCache::getReflection($class);
        // Prepare list of resolved mappings for this class to be cached
        if (!array_key_exists($class, $this->targetsCache)) {
            $this->targetsCache[$class] = [];
        }

        // Pre-process given object data
        /** @var DataProcessorInterface[] $processors */
        $processors = $this->getHandlers(DataProcessorInterface::class);
        foreach ($processors as $processor) {
            try {
                if (!$processor->canProcess($reflection, $data)) {
                    continue;
                }
                $data = $processor->process($reflection, $data);
            } catch (\Exception $e) {
                continue;
            }
        }

        $object = null;
        // Attempt to create object instance
        /** @var ObjectConstructorInterface[] $constructors */
        $constructors = $this->getHandlers(ObjectConstructorInterface::class);
        foreach ($constructors as $constructor) {
            try {
                if (!$constructor->canConstruct($reflection, $data)) {
                    continue;
                }
                $instance = $constructor->construct($reflection, $data);
                if (\is_object($instance) && (\get_class($instance) === $reflection->name || is_subclass_of($instance, $reflection->name))) {
                    $object = $instance;
                    break;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Prepare list target providers that can handle our class
        /** @var TargetProviderInterface[] $providers */
        $providers = array_filter($this->getHandlers(TargetProviderInterface::class), function (TargetProviderInterface $provider) use ($reflection) {
            return $provider->canGetTarget($reflection);
        });

        // Assign data to the object
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $this->targetsCache[$class])) {
                // We have no cached target for this data key, try to find it
                $target = null;
                foreach ($providers as $provider) {
                    try {
                        $ct = $provider->getTarget($reflection, $key);
                        if ($ct instanceof \ReflectionMethod || $ct instanceof \ReflectionProperty) {
                            $target = $ct;
                            break;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                if (!$target instanceof \Reflector) {
                    // Target is not available, mark it accordingly into targets cache
                    // so we will be able to throw exception in a case of strict object creation mode
                    $target = false;
                }
                $this->targetsCache[$class][$key] = $target;
            } else {
                $target = $this->targetsCache[$class][$key];
            }

            if ($target === false && $strict) {
                throw new \InvalidArgumentException(sprintf('Data item "%s" can\'t be assigned to class "%s"', $key, $class));
            }

            // Attempt to convert data value into type that is expected by object, being built
            /** @var TypeConverterInterface[] $converters */
            $converters = $this->getHandlers(TypeConverterInterface::class);
            foreach ($converters as $converter) {
                try {
                    if (!$converter->canConvert($target, $data, $key)) {
                        continue;
                    }
                    try {
                        $value = $converter->convert($target, $data, $key);
                        break;
                    } catch (NotConvertedException $e) {
                        continue;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Attempt to assign object value to the object
            $assigned = false;
            /** @var ValueAssignerInterface[] $assigners */
            $assigners = $this->getHandlers(ValueAssignerInterface::class);
            foreach ($assigners as $assigner) {
                try {
                    if (!$assigner->canAssign($object, $target, $value)) {
                        continue;
                    }
                    if ($assigner->assign($object, $target, $value)) {
                        $assigned = true;
                        break;
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
            if (!$assigned && $strict) {
                throw new \InvalidArgumentException(sprintf('Failed to assign object data item "%s" to object "%s"', $key, $class));
            }
        }

        return $object;
    }

    /**
     * Get handlers of given type
     * Implemented as separate method to allow assigning instance of object builder
     * to handlers that need it
     *
     * @param string $type
     * @return HandlerInterface[]
     */
    protected function getHandlers(string $type): array
    {
        $handlers = $this->handlers->getHandlers($type)->toArray();
        foreach ($handlers as $handler) {
            if ($handler instanceof ObjectBuilderAwareHandlerInterface) {
                $handler->setBuilder($this);
            }
        }
        return $handlers;
    }
}
