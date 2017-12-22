<?php

namespace Flying\ObjectBuilder;

use Flying\ObjectBuilder\Handler\ObjectBuilderAwareHandlerInterface;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Handler\TypeConverter\TypeConverterInterface;
use Flying\ObjectBuilder\Handler\ValueAssigner\ValueAssignerInterface;
use Flying\ObjectBuilder\Registry\HandlersList;
use Flying\ObjectBuilder\Registry\HandlersRegistryInterface;

class ObjectBuilder implements ObjectBuilderInterface
{
    /**
     * @var \ReflectionClass[]
     */
    private $reflections = [];
    /**
     * @var array
     */
    private $methods = [];
    /**
     * @var array
     */
    private $properties = [];
    /**
     * @var array
     */
    private $targetsCache = [];
    /**
     * @var TargetProviderInterface[]
     */
    private $providers;
    /**
     * @var TypeConverterInterface[]
     */
    private $converters;
    /**
     * @var ValueAssignerInterface[]
     */
    private $assigners;

    /**
     * @param HandlersRegistryInterface $handlers
     */
    public function __construct(HandlersRegistryInterface $handlers)
    {
        $init = function (HandlersList $handlers, &$target) {
            foreach ($handlers as $handler) {
                if ($handler instanceof ObjectBuilderAwareHandlerInterface) {
                    $handler->setBuilder($this);
                }
            }
            $target = $handlers->toArray();
        };
        $init($handlers->getHandlers(TargetProviderInterface::class), $this->providers);
        $init($handlers->getHandlers(TypeConverterInterface::class), $this->converters);
        $init($handlers->getHandlers(ValueAssignerInterface::class), $this->assigners);
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function build(string $class, array $data = [], $strict = false)
    {
        $reflection = $this->getReflection($class);
        // Prepare list of resolved mappings for this class to be cached
        if (!array_key_exists($class, $this->targetsCache)) {
            $this->targetsCache[$class] = [];
        }

        // Prepare list target providers that can handle our class
        /** @var TargetProviderInterface[] $providers */
        $providers = array_filter($this->providers, function (TargetProviderInterface $provider) use ($reflection) {
            return $provider->canGetTarget($reflection);
        });
        $methods = $this->getMethods($reflection);
        $properties = $this->getProperties($reflection);

        $object = new $class();

        // Assign data to the object
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $this->targetsCache[$class])) {
                // We have no cached target for this data key, try to find it
                $target = null;
                foreach ($providers as $provider) {
                    try {
                        if (!$provider->canGetTarget($reflection)) {
                            continue;
                        }
                        $ct = $provider->getTarget($reflection, $key);
                    } catch (\Exception $e) {
                        continue;
                    }
                    if ($ct instanceof \ReflectionMethod || $ct instanceof \ReflectionProperty) {
                        $target = $ct;
                        break;
                    }
                    if (!\is_string($ct)) {
                        continue;
                    }
                    if (array_key_exists($ct, $methods)) {
                        $target = $methods[$ct];
                        break;
                    }

                    if (array_key_exists($ct, $properties)) {
                        $target = $properties[$ct];
                        break;
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
            foreach ($this->converters as $converter) {
                try {
                    if (!$converter->canConvert($target, $value)) {
                        continue;
                    }
                    $v = $value;
                    if ($converter->convert($target, $v)) {
                        $value = $v;
                        break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Attempt to assign object value to the object
            $assigned = false;
            foreach ($this->assigners as $assigner) {
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
     * Get class reflection for given class name
     *
     * @param string $class
     * @return \ReflectionClass
     * @throws \InvalidArgumentException
     */
    protected function getReflection(string $class): \ReflectionClass
    {
        if (!array_key_exists($class, $this->reflections)) {
            try {
                $reflection = new \ReflectionClass($class);
            } /** @noinspection PhpRedundantCatchClauseInspection */ catch (\ReflectionException $e) {
                throw new \InvalidArgumentException(sprintf('Failed to create reflection for class "%s", it probably doesn\'t exists', $class));
            }
            $this->reflections[$class] = $reflection;
        }
        return $this->reflections[$class];
    }

    /**
     * Get list of method reflections for given class reflection
     *
     * @param \ReflectionClass $reflection
     * @return \ReflectionMethod[]
     */
    protected function getMethods(\ReflectionClass $reflection): array
    {
        $class = $reflection->name;
        if (!array_key_exists($class, $this->methods)) {
            // Create list of methods that we have in this class
            $methods = [];
            do {
                foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    if (!array_key_exists($method->getName(), $methods) && !$method->isAbstract()) {
                        $methods[$method->getName()] = $method;
                    }
                }
                $reflection = $reflection->getParentClass();
            } while ($reflection);
            $this->methods[$class] = $methods;
        }
        return $this->methods[$class];
    }

    /**
     * Get list of property reflections for given class reflection
     *
     * @param \ReflectionClass $reflection
     * @return \ReflectionProperty[]
     */
    protected function getProperties(\ReflectionClass $reflection): array
    {
        $class = $reflection->name;
        if (!array_key_exists($class, $this->properties)) {
            // Create list of properties that we have in this class
            $properties = [];
            do {
                foreach ($reflection->getProperties() as $property) {
                    if (!array_key_exists($property->getName(), $properties)) {
                        $properties[$property->getName()] = $property;
                    }
                }
                $reflection = $reflection->getParentClass();
            } while ($reflection);
            $this->properties[$class] = $properties;
        }
        return $this->properties[$class];
    }
}
