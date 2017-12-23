<?php

namespace Flying\ObjectBuilder\Tests;

use Flying\ObjectBuilder\Handler\DataProcessor\DataProcessorInterface;
use Flying\ObjectBuilder\Handler\ObjectConstructor\DefaultObjectConstructor;
use Flying\ObjectBuilder\Handler\TargetProvider\DefaultTargetProvider;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Handler\TypeConverter\ChildObjectConverter;
use Flying\ObjectBuilder\Handler\TypeConverter\DefaultTypeConverter;
use Flying\ObjectBuilder\Handler\ValueAssigner\DefaultValueAssigner;
use Flying\ObjectBuilder\ObjectBuilder;
use Flying\ObjectBuilder\ObjectBuilderInterface;
use Flying\ObjectBuilder\Registry\HandlersRegistry;
use Flying\ObjectBuilder\Registry\HandlersRegistryInterface;
use Flying\ObjectBuilder\Tests\Fixtures\Handler\TargetProvider\BuilderAwareHandlerTypeProvider;
use Flying\ObjectBuilder\Tests\Fixtures\Handler\TargetProvider\PrioritizedTypeProvider;
use Flying\ObjectBuilder\Tests\Fixtures\TestObject\ChildObject;
use Flying\ObjectBuilder\Tests\Fixtures\TestObject\MultiLevelObject;
use Flying\ObjectBuilder\Tests\Fixtures\TestObject\ScalarTypes;
use Flying\ObjectBuilder\Tests\Fixtures\TestObject\TestObjectInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Call\Call;

class ObjectBuilderTest extends TestCase
{
    /**
     * @param TestObjectInterface $object
     * @dataProvider dpTestObjects
     */
    public function testBasicBuilding(TestObjectInterface $object)
    {
        $builder = $this->getTestBuilder();
        $class = \get_class($object);
        /** @var TestObjectInterface $result */
        $result = $builder->build($class, $object->getBuildData());
        $this->assertInstanceOf($class, $result);
        $this->assertEquals($result->getExpectedResult(), $result->getActualResult());
    }

    public function dpTestObjects()
    {
        return [
            [new ScalarTypes()],
        ];
    }

    public function testPriorityHandlersAreSortedByPriority()
    {
        $h1 = $this->prophesize(PrioritizedTypeProvider::class);
        $h1
            ->getPriority()
            ->shouldBeCalled()
            ->willReturn(0);
        /** @noinspection PhpParamsInspection */
        $h1
            ->canGetTarget(Argument::type(\ReflectionClass::class))
            ->shouldBeCalled()
            ->will(function () use (&$calls) {
                $calls[] = 1;
                return false;
            });
        $h2 = $this->prophesize(PrioritizedTypeProvider::class);
        $h2
            ->getPriority()
            ->shouldBeCalled()
            ->willReturn(10);
        /** @noinspection PhpParamsInspection */
        $h2
            ->canGetTarget(Argument::type(\ReflectionClass::class))
            ->shouldBeCalled()
            ->will(function () use (&$calls) {
                $calls[] = 2;
                return false;
            });

        $calls = [];
        $builder = new ObjectBuilder(new HandlersRegistry([
            $h1->reveal(),
            $h2->reveal(),
        ]));
        $builder->build(\stdClass::class, ['a' => true]);
        $this->assertEquals([2, 1], $calls);

        $calls = [];
        $builder = new ObjectBuilder(new HandlersRegistry([
            $h2->reveal(),
            $h1->reveal(),
        ]));
        $builder->build(\stdClass::class, ['a' => true]);
        $this->assertEquals([2, 1], $calls);
    }

    public function testHandlersWithNoPriorityShouldBeUsedIntoDefinedOrder()
    {
        $calls = [];
        $h1 = $this->prophesize(TargetProviderInterface::class);
        /** @noinspection PhpParamsInspection */
        $h1
            ->canGetTarget(Argument::type(\ReflectionClass::class))
            ->shouldBeCalled()
            ->will(function () use (&$calls) {
                $calls[] = 1;
                return false;
            });

        $h2 = $this->prophesize(TargetProviderInterface::class);
        /** @noinspection PhpParamsInspection */
        $h2
            ->canGetTarget(Argument::type(\ReflectionClass::class))
            ->shouldBeCalled()
            ->will(function () use (&$calls) {
                $calls[] = 2;
                return false;
            });

        $builder = new ObjectBuilder(new HandlersRegistry([
            $h1->reveal(),
            $h2->reveal(),
        ]));
        $builder->build(\stdClass::class, ['a' => true]);
        $this->assertEquals([1, 2], $calls);
    }

    public function testObjectBuilderAwareHandlersShouldGetInstanceOfBuilder()
    {
        $handler = $this->prophesize(BuilderAwareHandlerTypeProvider::class);
        /** @noinspection PhpParamsInspection, NullPointerExceptionInspection */
        $handler
            ->setBuilder(Argument::type(ObjectBuilder::class))
            ->shouldBeCalled();
        /** @noinspection PhpParamsInspection */
        $handler
            ->canGetTarget(Argument::type(\ReflectionClass::class))
            ->shouldBeCalled()
            ->willReturn(false);
        $builder = new ObjectBuilder(new HandlersRegistry([
            $handler->reveal(),
        ]));
        $builder->build(\stdClass::class, ['a' => true]);
        $calls = $handler->findProphecyMethodCalls('setBuilder', new Argument\ArgumentsWildcard([Argument::type(ObjectBuilder::class)]));
        $this->assertCount(1, $calls);
        /** @var Call $call */
        $call = array_shift($calls);
        $this->assertSame($builder, $call->getArguments()[0]);
    }

    public function testObjectHandlerCanBeSharedAmongDifferentObjectBuilders()
    {
        $calls = [];
        $handler = $this->prophesize(BuilderAwareHandlerTypeProvider::class);
        /** @noinspection PhpParamsInspection, NullPointerExceptionInspection */
        $handler
            ->setBuilder(Argument::type(ObjectBuilder::class))
            ->shouldBeCalled()
            ->will(function ($args) use (&$calls) {
                $calls[] = $args[0];
            });
        /** @noinspection PhpParamsInspection */
        $handler
            ->canGetTarget(Argument::type(\ReflectionClass::class))
            ->shouldBeCalled()
            ->willReturn(false);
        $handlers = new HandlersRegistry([
            $handler->reveal(),
        ]);
        $b1 = new ObjectBuilder($handlers);
        $b2 = new ObjectBuilder($handlers);

        $b1->build(\stdClass::class);
        $b2->build(\stdClass::class);

        $this->assertSame($b1, $calls[0]);
        $this->assertSame($b2, $calls[1]);
    }

    public function testBuildingMultiLevelObjects()
    {
        $registry = $this->getTestRegistry();
        $registry->addHandlers(new ChildObjectConverter());
        $builder = new ObjectBuilder($registry);

        /** @var MultiLevelObject $object */
        $object = $builder->build(MultiLevelObject::class, ['child' => []]);
        $this->assertInstanceOf(ChildObject::class, $object->getChild());
        /** @var MultiLevelObject $object */
        $object = $builder->build(MultiLevelObject::class, [
            'child' => [
                'value' => 'abc',
                'child' => [
                    'value' => 'xyz',
                ]
            ]
        ]);
        $this->assertInstanceOf(ChildObject::class, $object->getChild());
        $this->assertEquals('abc', $object->getChild()->getValue());
        $this->assertInstanceOf(ChildObject::class, $object->getChild()->getChild());
        $this->assertEquals('xyz', $object->getChild()->getChild()->getValue());
    }

    public function testUseOfDataProcessor()
    {
        $registry = $this->getTestRegistry();
        $registry->addHandlers(new class implements DataProcessorInterface {
            public function canProcess(\ReflectionClass $reflection, array $data): bool
            {
                return true;
            }

            public function process(\ReflectionClass $reflection, array $data): array
            {
                return (new ScalarTypes())->getBuildData();
            }
        });
        $builder = new ObjectBuilder($registry);
        $object = $builder->build(ScalarTypes::class);
        /** @noinspection NullPointerExceptionInspection */
        $this->assertEquals($object->getExpectedResult(), $object->getActualResult());
    }

    /**
     * @return ObjectBuilderInterface
     */
    protected function getTestBuilder(): ObjectBuilderInterface
    {
        return new ObjectBuilder($this->getTestRegistry());
    }

    /**
     * @return HandlersRegistryInterface
     */
    protected function getTestRegistry(): HandlersRegistryInterface
    {
        return new HandlersRegistry([
            new DefaultObjectConstructor(),
            new DefaultTargetProvider(),
            new DefaultTypeConverter(),
            new DefaultValueAssigner(),
        ]);
    }
}
