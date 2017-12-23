<?php

namespace Flying\ObjectBuilder\Tests\Registry;

use Flying\ObjectBuilder\Handler\DataProcessor\DataProcessorInterface;
use Flying\ObjectBuilder\Handler\ObjectConstructor\ObjectConstructorInterface;
use Flying\ObjectBuilder\Handler\TargetProvider\DefaultTargetProvider;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Handler\TypeConverter\DefaultTypeConverter;
use Flying\ObjectBuilder\Handler\TypeConverter\TypeConverterInterface;
use Flying\ObjectBuilder\Handler\ValueAssigner\DefaultValueAssigner;
use Flying\ObjectBuilder\Handler\ValueAssigner\ValueAssignerInterface;
use Flying\ObjectBuilder\Registry\HandlersList;
use Flying\ObjectBuilder\Registry\HandlersListInterface;
use Flying\ObjectBuilder\Registry\HandlersRegistry;
use Flying\ObjectBuilder\Registry\HandlersRegistryInterface;
use Flying\ObjectBuilder\Tests\Registry\Fixtures\UniversalHandler;
use PHPUnit\Framework\TestCase;

class HandlersRegistryTest extends TestCase
{
    public function testRequestForSingleTypeReturnsSingleList()
    {
        $registry = new HandlersRegistry();
        $this->assertInstanceOf(HandlersListInterface::class, $registry->getHandlers(TargetProviderInterface::class));
    }

    public function testSettingSingleHandler()
    {
        $handler = new DefaultTargetProvider();

        $test = function (HandlersRegistryInterface $registry) use ($handler) {
            $this->assertCount(1, $registry->getHandlers(TargetProviderInterface::class));
            $this->assertCount(0, $registry->getHandlers(TypeConverterInterface::class));
            $this->assertCount(0, $registry->getHandlers(ValueAssignerInterface::class));
            $list = $registry->getHandlers(TargetProviderInterface::class);
            $this->assertSame($handler, $list->toArray()[0]);
        };

        $registry = new HandlersRegistry($handler);
        $test($registry);

        $registry = new HandlersRegistry();
        $registry->addHandlers($handler);
        $test($registry);
    }

    public function testSettingMultipleHandlersAsArray()
    {
        $handlers = [
            new DefaultTargetProvider(),
            new DefaultTypeConverter(),
            new DefaultValueAssigner(),
        ];

        $test = function (HandlersRegistryInterface $registry) {
            $this->assertCount(1, $registry->getHandlers(TargetProviderInterface::class));
            $this->assertCount(1, $registry->getHandlers(TypeConverterInterface::class));
            $this->assertCount(1, $registry->getHandlers(TypeConverterInterface::class));
        };

        $registry = new HandlersRegistry($handlers);
        $test($registry);

        $registry = new HandlersRegistry();
        $registry->addHandlers($handlers);
        $test($registry);
    }

    public function testSettingMultipleHandlersAsList()
    {
        $h1 = new DefaultTargetProvider();
        $h2 = new DefaultTargetProvider();
        $list = new HandlersList(TargetProviderInterface::class, [$h1, $h2]);

        $test = function (HandlersRegistryInterface $registry) use ($list, $h1, $h2) {
            $this->assertCount(2, $registry->getHandlers(TargetProviderInterface::class));
            $this->assertCount(0, $registry->getHandlers(TypeConverterInterface::class));
            $this->assertCount(0, $registry->getHandlers(ValueAssignerInterface::class));

            $list = $registry->getHandlers(TargetProviderInterface::class);
            $this->assertSame($h1, $list->toArray()[0]);
            $this->assertSame($h2, $list->toArray()[1]);
        };

        $registry = new HandlersRegistry($list);
        $test($registry);

        $registry = new HandlersRegistry();
        $registry->addHandlers($list);
        $test($registry);
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

        $test = function (HandlersRegistryInterface $registry) {
            $this->assertCount(2, $registry->getHandlers(TargetProviderInterface::class));
            $this->assertCount(3, $registry->getHandlers(TypeConverterInterface::class));
            $this->assertCount(0, $registry->getHandlers(ValueAssignerInterface::class));
        };

        $handlers = [$l1, $l2];
        $registry = new HandlersRegistry($handlers);
        $test($registry);

        $registry = new HandlersRegistry();
        $registry->addHandlers($handlers);
        $test($registry);
    }

    public function testSettingMultipleHandlersAsRegistry()
    {
        $h1 = new DefaultTargetProvider();
        $h2 = new DefaultTargetProvider();
        $handlers = new HandlersRegistry([$h1, $h2]);

        $test = function (HandlersRegistryInterface $registry) {
            $this->assertCount(2, $registry->getHandlers(TargetProviderInterface::class));
            $this->assertCount(0, $registry->getHandlers(TypeConverterInterface::class));
            $this->assertCount(0, $registry->getHandlers(ValueAssignerInterface::class));
        };

        $registry = new HandlersRegistry($handlers);
        $test($registry);

        $registry = new HandlersRegistry();
        $registry->addHandlers($handlers);
        $test($registry);
    }

    public function testSettingMultipleHandlersAsRegistryArray()
    {
        $h1 = new DefaultTargetProvider();
        $h2 = new DefaultTargetProvider();
        $r1 = new HandlersRegistry([$h1, $h2]);

        $h3 = new DefaultTypeConverter();
        $h4 = new DefaultTypeConverter();
        $h5 = new DefaultTypeConverter();
        $r2 = new HandlersRegistry([$h3, $h4, $h5]);

        $handlers = [$r1, $r2];

        $test = function (HandlersRegistryInterface $registry) {
            $this->assertCount(2, $registry->getHandlers(TargetProviderInterface::class));
            $this->assertCount(3, $registry->getHandlers(TypeConverterInterface::class));
            $this->assertCount(0, $registry->getHandlers(ValueAssignerInterface::class));
        };

        $registry = new HandlersRegistry($handlers);
        $test($registry);

        $registry = new HandlersRegistry();
        $registry->addHandlers($handlers);
        $test($registry);
    }

    public function testHandlerThatImplementsMultipleTypesShouldBeAssignedToAllAppropriateLists()
    {
        $handler = new UniversalHandler();

        $test = function (HandlersRegistryInterface $registry) {
            $this->assertCount(1, $registry->getHandlers(DataProcessorInterface::class));
            $this->assertCount(1, $registry->getHandlers(ObjectConstructorInterface::class));
            $this->assertCount(1, $registry->getHandlers(TargetProviderInterface::class));
            $this->assertCount(1, $registry->getHandlers(TypeConverterInterface::class));
            $this->assertCount(1, $registry->getHandlers(ValueAssignerInterface::class));
        };

        $registry = new HandlersRegistry($handler);
        $test($registry);

        $registry = new HandlersRegistry();
        $registry->addHandlers($handler);
        $test($registry);
    }

    /**
     * @param mixed $handlers
     * @expectedException \Flying\ObjectBuilder\Exception\HandlerFailureException
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
     * @expectedException \Flying\ObjectBuilder\Exception\HandlerFailureException
     */
    public function testRetrievingInvalidHandlerTypeShouldFail()
    {
        $registry = new HandlersRegistry();
        $registry->getHandlers('unknown type');
    }
}
