<?php

namespace Flying\ObjectBuilder\Tests\Handler\TypeConverter;

use Flying\ObjectBuilder\Handler\ObjectConstructor\DefaultObjectConstructor;
use Flying\ObjectBuilder\Handler\ObjectConstructor\ObjectConstructorInterface;
use Flying\ObjectBuilder\Handler\TargetProvider\DefaultTargetProvider;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Handler\TypeConverter\ChildObjectConverter;
use Flying\ObjectBuilder\Handler\TypeConverter\DefaultTypeConverter;
use Flying\ObjectBuilder\Handler\ValueAssigner\DefaultValueAssigner;
use Flying\ObjectBuilder\ObjectBuilder;
use Flying\ObjectBuilder\ObjectBuilderInterface;
use Flying\ObjectBuilder\Registry\HandlersRegistry;
use Flying\ObjectBuilder\Registry\HandlersRegistryInterface;
use Flying\ObjectBuilder\Tests\Handler\TypeConverter\Fixtures\AbstractChild;
use Flying\ObjectBuilder\Tests\Handler\TypeConverter\Fixtures\Child;
use Flying\ObjectBuilder\Tests\Handler\TypeConverter\Fixtures\ChildInterface;
use Flying\ObjectBuilder\Tests\Handler\TypeConverter\Fixtures\ParentObject;
use PHPUnit\Framework\TestCase;

class ChildObjectConverterTest extends TestCase
{
    /**
     * @param string $method
     * @param array $data
     * @param string $key
     * @param bool $expected
     * @dataProvider dpMethodTests
     */
    public function testHandlingAbilityTests(string $method, array $data, string $key, bool $expected)
    {
        $converter = new ChildObjectConverter();
        $reflection = new \ReflectionClass(ParentObject::class);
        $this->assertTrue($reflection->hasMethod($method));
        $methodReflection = $reflection->getMethod($method);
        $this->assertEquals($expected, $converter->canConvert($methodReflection, $data, $key));
    }

    public function dpMethodTests()
    {
        return [
            ['setChildByNormalClass', [], 'child', false],
            ['setChildByNormalClass', ['child' => ['value' => 'success']], 'child', true],
            ['setChildByAbstractClass', ['child' => ['value' => 'success']], 'child', true],
            ['setChildByInterface', ['child' => ['value' => 'success']], 'child', true],
            ['setterWithGenericArgument', ['child' => ['value' => 'success']], 'child', false],
            ['setterWithNonObjectArgument', ['child' => ['value' => 'success']], 'child', false],
            ['setterWithMultipleArguments', ['child' => ['value' => 'success']], 'child', false],
        ];
    }

    public function testSettingChildObjectThroughNormalClass()
    {
        $builder = $this->getTestBuilder([
            new class implements TargetProviderInterface
            {
                public function canGetTarget(\ReflectionClass $reflection): bool
                {
                    return $reflection->name === ParentObject::class;
                }

                public function getTarget(\ReflectionClass $reflection, string $name): ?\Reflector
                {
                    return $name === 'child' ? $reflection->getMethod('setChildByNormalClass') : null;
                }
            },
        ]);
        $object = $builder->build(ParentObject::class, ['child' => ['value' => 'success']]);
        /** @var ParentObject $object */
        $this->assertInstanceOf(ParentObject::class, $object);
        /** @noinspection NullPointerExceptionInspection */
        $this->assertInstanceOf(Child::class, $object->getChild());
        /** @noinspection NullPointerExceptionInspection */
        $this->assertEquals('success', $object->getChild()->getValue());
    }

    public function testSettingChildObjectThroughAbstractClass()
    {
        $builder = $this->getTestBuilder([
            new class implements TargetProviderInterface
            {
                public function canGetTarget(\ReflectionClass $reflection): bool
                {
                    return $reflection->name === ParentObject::class;
                }

                public function getTarget(\ReflectionClass $reflection, string $name): ?\Reflector
                {
                    return $name === 'child' ? $reflection->getMethod('setChildByAbstractClass') : null;
                }
            },
            new class implements ObjectConstructorInterface
            {
                public function canConstruct(\ReflectionClass $reflection, array $data): bool
                {
                    return $reflection->name === AbstractChild::class;
                }

                public function construct(\ReflectionClass $reflection, array &$data)
                {
                    return new Child();
                }
            },
        ]);
        $object = $builder->build(ParentObject::class, ['child' => ['value' => 'success']]);
        /** @var ParentObject $object */
        $this->assertInstanceOf(ParentObject::class, $object);
        /** @noinspection NullPointerExceptionInspection */
        $this->assertInstanceOf(Child::class, $object->getChild());
        /** @noinspection NullPointerExceptionInspection */
        $this->assertEquals('success', $object->getChild()->getValue());
    }

    public function testSettingChildObjectThroughInterface()
    {
        $builder = $this->getTestBuilder([
            new class implements TargetProviderInterface
            {
                public function canGetTarget(\ReflectionClass $reflection): bool
                {
                    return $reflection->name === ParentObject::class;
                }

                public function getTarget(\ReflectionClass $reflection, string $name): ?\Reflector
                {
                    return $name === 'child' ? $reflection->getMethod('setChildByInterface') : null;
                }
            },
            new class implements ObjectConstructorInterface
            {
                public function canConstruct(\ReflectionClass $reflection, array $data): bool
                {
                    return $reflection->name === ChildInterface::class;
                }

                public function construct(\ReflectionClass $reflection, array &$data)
                {
                    return new Child();
                }
            },
        ]);
        $object = $builder->build(ParentObject::class, ['child' => ['value' => 'success']]);
        /** @var ParentObject $object */
        $this->assertInstanceOf(ParentObject::class, $object);
        /** @noinspection NullPointerExceptionInspection */
        $this->assertInstanceOf(Child::class, $object->getChild());
        /** @noinspection NullPointerExceptionInspection */
        $this->assertEquals('success', $object->getChild()->getValue());
    }

    public function testFailedBuildOfChildObjectShouldNotBreakMainObjectBuildInNormalMode()
    {
        $builder = $this->getTestBuilder([
            new class implements TargetProviderInterface
            {
                public function canGetTarget(\ReflectionClass $reflection): bool
                {
                    return $reflection->name === ParentObject::class;
                }

                public function getTarget(\ReflectionClass $reflection, string $name): ?\Reflector
                {
                    return $name === 'child' ? $reflection->getMethod('setChildByInterface') : null;
                }
            },
            new class implements ObjectConstructorInterface
            {
                public function canConstruct(\ReflectionClass $reflection, array $data): bool
                {
                    return $reflection->name === ChildInterface::class;
                }

                public function construct(\ReflectionClass $reflection, array &$data)
                {
                    return new \stdClass();
                }
            },
        ]);
        $object = $builder->build(ParentObject::class, ['child' => ['value' => 'success']]);
        /** @var ParentObject $object */
        $this->assertInstanceOf(ParentObject::class, $object);
        /** @noinspection NullPointerExceptionInspection */
        $this->assertNull($object->getChild());
    }

    /**
     * @expectedException \Flying\ObjectBuilder\Exception\BuildFailedException
     */
    public function testFailedBuildOfChildObjectShouldBreakMainObjectBuildInStrictMode()
    {
        $builder = $this->getTestBuilder([
            new class implements TargetProviderInterface
            {
                public function canGetTarget(\ReflectionClass $reflection): bool
                {
                    return $reflection->name === ParentObject::class;
                }

                public function getTarget(\ReflectionClass $reflection, string $name): ?\Reflector
                {
                    return $name === 'child' ? $reflection->getMethod('setChildByInterface') : null;
                }
            },
            new class implements ObjectConstructorInterface
            {
                public function canConstruct(\ReflectionClass $reflection, array $data): bool
                {
                    return $reflection->name === ChildInterface::class;
                }

                public function construct(\ReflectionClass $reflection, array &$data)
                {
                    return new \stdClass();
                }
            },
        ]);
        $builder->build(ParentObject::class, ['child' => ['value' => 'success']], [ObjectBuilderInterface::STRICT=>true]);
    }

    /**
     * @param array $handlers
     * @return ObjectBuilder
     */
    protected function getTestBuilder(array $handlers = []): ObjectBuilder
    {
        return new ObjectBuilder($this->getTestRegistry($handlers));
    }

    /**
     * @param array $handlers
     * @return HandlersRegistryInterface
     */
    protected function getTestRegistry(array $handlers = []): HandlersRegistryInterface
    {
        return new HandlersRegistry(array_merge([
            new DefaultObjectConstructor(),
            new DefaultTargetProvider(),
            new ChildObjectConverter(),
            new DefaultTypeConverter(),
            new DefaultValueAssigner(),
        ], $handlers));
    }
}
