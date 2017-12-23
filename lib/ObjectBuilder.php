<?php

namespace Flying\ObjectBuilder;

use Flying\ObjectBuilder\Exception\BuildFailedException;
use Flying\ObjectBuilder\Exception\DebugException;
use Flying\ObjectBuilder\Exception\NotConvertedException;
use Flying\ObjectBuilder\Exception\ReflectionException;
use Flying\ObjectBuilder\Handler\DataProcessor\DataProcessorInterface;
use Flying\ObjectBuilder\Handler\HandlerInterface;
use Flying\ObjectBuilder\Handler\ObjectBuilderAwareHandlerInterface;
use Flying\ObjectBuilder\Handler\ObjectConstructor\ObjectConstructorInterface;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
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
     */
    public function build(string $class, array $data = [], array $options = [])
    {
        // Normalize options
        $options = $this->getOptions($options);
        try {
            $reflection = ReflectionCache::getReflection($class);

            // Pre-process given object data
            /** @var DataProcessorInterface[] $processors */
            $processors = $this->getHandlers(DataProcessorInterface::class);
            foreach ($processors as $processor) {
                try {
                    if (!$processor->canProcess($reflection, $data)) {
                        continue;
                    }
                    $data = $processor->process($reflection, $data);
                } catch (BuildFailedException $e) {
                    throw $e;
                } catch (\Exception $e) {
                    if ($options[self::DEBUG]) {
                        throw new DebugException($e);
                    }
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
                } catch (BuildFailedException $e) {
                    throw $e;
                } catch (\Exception $e) {
                    if ($options[self::DEBUG]) {
                        throw new DebugException($e);
                    }
                    continue;
                }
            }
            if (!\is_object($object)) {
                throw new BuildFailedException(sprintf('Failed to create instance of "%s" object', $class));
            }
            // It is possible that actual object instance is not the same as class name, initially passed to object builder
            // for example in a case if object builder receives name of interface and custom object constructor creates
            // instance of some particular object that implements this interface. We need to update class name and its reflection
            // so they will match actual object instance
            /** @var object $object */
            $class = \get_class($object);
            $reflection = ReflectionCache::getReflection($class);

            // Prepare lists of  handlers of all kinds will be used for building object
            /** @var TargetProviderInterface[] $providers */
            $providers = array_filter($this->getHandlers(TargetProviderInterface::class), function (TargetProviderInterface $provider) use ($reflection) {
                return $provider->canGetTarget($reflection);
            });
            /** @var TypeConverterInterface[] $converters */
            $converters = $this->getHandlers(TypeConverterInterface::class);
            /** @var ValueAssignerInterface[] $assigners */
            $assigners = $this->getHandlers(ValueAssignerInterface::class);

            // Prepare list of resolved mappings for this class to be cached
            if (!array_key_exists($class, $this->targetsCache)) {
                $this->targetsCache[$class] = [];
            }

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
                        } catch (BuildFailedException $e) {
                            throw $e;
                        } catch (\Exception $e) {
                            if ($options[self::DEBUG]) {
                                throw new DebugException($e);
                            }
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

                if ($target === false) {
                    if ($options[self::STRICT]) {
                        throw new BuildFailedException(sprintf('Data item "%s" can\'t be assigned to class "%s"', $key, $class));
                    }
                    continue;
                }

                // Attempt to convert data value into type that is expected by object, being built
                foreach ($converters as $converter) {
                    try {
                        if (!$converter->canConvert($target, $data, $key)) {
                            continue;
                        }
                        try {
                            $value = $converter->convert($target, $data, $key);
                            break;
                        } catch (BuildFailedException $e) {
                            throw $e;
                        } catch (NotConvertedException $e) {
                            continue;
                        }
                    } catch (BuildFailedException $e) {
                        throw $e;
                    } catch (\Exception $e) {
                        if ($options[self::DEBUG]) {
                            throw new DebugException($e);
                        }
                        continue;
                    }
                }

                // Attempt to assign object value to the object
                $assigned = false;
                foreach ($assigners as $assigner) {
                    try {
                        if (!$assigner->canAssign($object, $target, $value)) {
                            continue;
                        }
                        if ($assigner->assign($object, $target, $value)) {
                            $assigned = true;
                            break;
                        }
                    } catch (BuildFailedException $e) {
                        throw $e;
                    } catch (\Throwable $e) {
                        if ($options[self::DEBUG]) {
                            throw new DebugException($e);
                        }
                        continue;
                    }
                }
                if (!$assigned && $options[self::STRICT]) {
                    throw new BuildFailedException(sprintf('Failed to assign object data item "%s" to object "%s"', $key, $class));
                }
            }

            return $object;
        } catch (ReflectionException $e) {
            if ($options[self::DEBUG]) {
                throw new DebugException($e);
            }
            BuildFailedException::throw($e);
        } catch (BuildFailedException $e) {
            if ($options[self::DEBUG]) {
                throw new DebugException($e);
            }
            throw $e;
        } catch (\Throwable $e) {
            if ($options[self::DEBUG]) {
                throw new DebugException($e);
            }
            BuildFailedException::throw($e);
        }
        return null;
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

    /**
     * Normalize given object builder options
     *
     * @param array $options
     * @return array
     */
    protected function getOptions(array $options): array
    {
        $defaults = [
            self::STRICT => false,
            self::DEBUG  => false,
        ];
        $result = $defaults;
        foreach ($options as $key => $value) {
            switch ($key) {
                case self::STRICT:
                case self::DEBUG:
                    $value = (boolean)$value;
                    break;
            }
            $result[$key] = $value;
        }
        return $result;
    }
}
