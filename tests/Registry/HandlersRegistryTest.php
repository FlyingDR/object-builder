<?php

namespace Flying\ObjectBuilder\Tests\Registry;

use Flying\ObjectBuilder\Handler\TargetProvider\DefaultTargetProvider;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Handler\TypeConverter\DefaultTypeConverter;
use Flying\ObjectBuilder\Handler\TypeConverter\TypeConverterInterface;
use Flying\ObjectBuilder\Handler\ValueAssigner\DefaultValueAssigner;
use Flying\ObjectBuilder\Handler\ValueAssigner\ValueAssignerInterface;
use Flying\ObjectBuilder\Registry\HandlersList;
use Flying\ObjectBuilder\Registry\HandlersListInterface;
use Flying\ObjectBuilder\Registry\HandlersRegistry;
use PHPUnit\Framework\TestCase;

class HandlersRegistryTest extends TestCase
{
    public function testRegistryHaveListsForAllHandlerTypesByDefault()
    {
        $types = [
            TargetProviderInterface::class,
            TypeConverterInterface::class,
            ValueAssignerInterface::class,
        ];
        $registry = new HandlersRegistry();
        $handlers = $registry->getHandlers();
        $this->assertTrue(\is_array($handlers));
        $this->assertEquals(\count($types), \count($handlers));
        $this->assertEquals($types, array_keys($handlers));
        foreach ($handlers as $list) {
            $this->assertInstanceOf(HandlersListInterface::class, $list);
        }
    }

    public function testRequestForSingleTypeReturnsSingleList()
    {
        $registry = new HandlersRegistry();
        $this->assertInstanceOf(HandlersListInterface::class, $registry->getHandlers(TargetProviderInterface::class));
    }

    public function testSettingSingleHandler()
    {
        $handler = new DefaultTargetProvider();
        $registry = new HandlersRegistry($handler);
        $this->assertCount(1, $registry->getHandlers(TargetProviderInterface::class));
        $this->assertCount(0, $registry->getHandlers(TypeConverterInterface::class));
        $this->assertCount(0, $registry->getHandlers(ValueAssignerInterface::class));
        $list = $registry->getHandlers(TargetProviderInterface::class);
        $this->assertSame($handler, $list->toArray()[0]);
    }

    public function testSettingMultipleHandlersAsArray()
    {
        $registry = new HandlersRegistry([
            new DefaultTargetProvider(),
            new DefaultTypeConverter(),
            new DefaultValueAssigner(),
        ]);
        $this->assertCount(1, $registry->getHandlers(TargetProviderInterface::class));
        $this->assertCount(1, $registry->getHandlers(TypeConverterInterface::class));
        $this->assertCount(1, $registry->getHandlers(TypeConverterInterface::class));
    }

    public function testSettingMultipleHandlersAsList()
    {
        $h1 = new DefaultTargetProvider();
        $h2 = new DefaultTargetProvider();
        $list = new HandlersList(TargetProviderInterface::class, [$h1, $h2]);
        $registry = new HandlersRegistry($list);
        $this->assertCount(2, $registry->getHandlers(TargetProviderInterface::class));
        $this->assertCount(0, $registry->getHandlers(TypeConverterInterface::class));
        $this->assertCount(0, $registry->getHandlers(ValueAssignerInterface::class));

        $list = $registry->getHandlers(TargetProviderInterface::class);
        $this->assertSame($h1, $list->toArray()[0]);
        $this->assertSame($h2, $list->toArray()[1]);
    }

    public function testSettingMultipleHandlersAsListsArray()
    {
        $h1 = new DefaultTargetProvider();
        $h2 = new DefaultTargetProvider();
        $l1 = new HandlersList(TargetProviderInterface::class, [$h1, $h2]);
        $h3 = new DefaultTypeConverter();
        $h4 = new DefaultTypeConverter();
        $h5 = new DefaultTypeConverter();
        $l2 = new HandlersList(TypeConverterInterface::class, [$h3, $h4, $h5]);
        $registry = new HandlersRegistry([$l1, $l2]);
        $this->assertCount(2, $registry->getHandlers(TargetProviderInterface::class));
        $this->assertCount(3, $registry->getHandlers(TypeConverterInterface::class));
        $this->assertCount(0, $registry->getHandlers(ValueAssignerInterface::class));
    }

    /**
     * @param mixed $handlers
     * @expectedException \InvalidArgumentException
     * @dataProvider dpInvalidData
     */
    public function testPassingInvalidDataShouldFail($handlers)
    {
        new HandlersRegistry($handlers);
    }

    public function dpInvalidData()
    {
        return [
            [true],
            [123],
            ['abc'],
            [[1, 2, 3]],
            [new \DateTime()],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRetrievingInvalidHandlerTypeShouldFail()
    {
        $registry = new HandlersRegistry();
        $registry->getHandlers('unknown type');
    }
}
