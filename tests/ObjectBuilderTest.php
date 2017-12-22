<?php

namespace Flying\ObjectBuilder\Tests;

use Flying\ObjectBuilder\ObjectBuilder;
use Flying\ObjectBuilder\ObjectBuilderInterface;
use Flying\ObjectBuilder\TargetProvider\DefaultTargetProvider;
use Flying\ObjectBuilder\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Tests\Fixtures\TargetProvider\BuilderAwareTypeProvider;
use Flying\ObjectBuilder\Tests\Fixtures\TargetProvider\PrioritizedTypeProvider;
use Flying\ObjectBuilder\Tests\Fixtures\TestObject\ScalarTypes;
use Flying\ObjectBuilder\Tests\Fixtures\TestObject\TestObjectInterface;
use Flying\ObjectBuilder\TypeConverter\DefaultTypeConverter;
use Flying\ObjectBuilder\ValueAssigner\DefaultValueAssigner;
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
        $builder = new ObjectBuilder([
            $h1->reveal(),
            $h2->reveal(),
        ]);
        $builder->build(\stdClass::class, ['a' => true]);
        $this->assertEquals([2, 1], $calls);

        $calls = [];
        $builder = new ObjectBuilder([
            $h2->reveal(),
            $h1->reveal(),
        ]);
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

        $builder = new ObjectBuilder([
            $h1->reveal(),
            $h2->reveal(),
        ]);
        $builder->build(\stdClass::class, ['a' => true]);
        $this->assertEquals([1, 2], $calls);
    }

    public function testObjectBuilderAwareHandlersShouldGetInstanceOfBuilder()
    {
        $handler = $this->prophesize(BuilderAwareTypeProvider::class);
        /** @noinspection PhpParamsInspection, NullPointerExceptionInspection */
        $handler
            ->setBuilder(Argument::type(ObjectBuilder::class))
            ->shouldBeCalled();
        /** @noinspection PhpParamsInspection */
        $handler
            ->canGetTarget(Argument::type(\ReflectionClass::class))
            ->shouldBeCalled()
            ->willReturn(false);
        $builder = new ObjectBuilder([
            $handler->reveal(),
        ]);
        $builder->build(\stdClass::class, ['a' => true]);
        $calls = $handler->findProphecyMethodCalls('setBuilder', new Argument\ArgumentsWildcard([Argument::type(ObjectBuilder::class)]));
        $this->assertCount(1, $calls);
        /** @var Call $call */
        $call = array_shift($calls);
        $this->assertSame($builder, $call->getArguments()[0]);
    }

    /**
     * @return ObjectBuilderInterface
     */
    public function getTestBuilder(): ObjectBuilderInterface
    {
        return new ObjectBuilder([
            new DefaultTargetProvider(),
        ], [
            new DefaultTypeConverter(),
        ], [
            new DefaultValueAssigner(),
        ]);
    }
}
