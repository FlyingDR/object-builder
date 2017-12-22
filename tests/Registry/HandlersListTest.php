<?php

namespace Flying\ObjectBuilder\Tests\Registry;

use Flying\ObjectBuilder\Handler\TargetProvider\DefaultTargetProvider;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Handler\TypeConverter\DefaultTypeConverter;
use Flying\ObjectBuilder\Registry\HandlersList;
use Flying\ObjectBuilder\Tests\Fixtures\Handler\TargetProvider\PrioritizedTypeProvider;
use PHPUnit\Framework\TestCase;

class HandlersListTest extends TestCase
{
    /**
     * @param string $interface
     * @param array $items
     * @param boolean $shouldFail
     * @dataProvider dpItems
     */
    public function testItemsPassedInConstructorShouldBeValidatedAgainstInterface($interface, $items, $shouldFail)
    {
        if ($shouldFail) {
            $this->expectException(\InvalidArgumentException::class);
        }
        $list = new HandlersList($interface, $items);
        $this->assertSame($items, $list->toArray());
    }

    public function dpItems()
    {
        return [
            [
                TargetProviderInterface::class,
                [new DefaultTargetProvider()],
                false,
            ],
            [
                TargetProviderInterface::class,
                [new DefaultTypeConverter()],
                true,
            ],
            [
                TargetProviderInterface::class,
                [new DefaultTargetProvider(), new DefaultTypeConverter()],
                true,
            ],
            [
                TargetProviderInterface::class,
                [true, false, 123, 'abc'],
                true,
            ],
        ];
    }

    public function testListManipulations()
    {
        $list = new HandlersList(TargetProviderInterface::class);
        $this->assertTrue($list->isEmpty());
        $this->assertEquals(0, $list->count());
        $this->assertEquals([], $list->toArray());

        $items = [
            new DefaultTargetProvider(),
            new DefaultTargetProvider(),
        ];
        $list->set($items);
        $this->assertFalse($list->isEmpty());
        $this->assertEquals(2, $list->count());
        $this->assertEquals($items, $list->toArray());

        $additional = new DefaultTargetProvider();
        $this->assertTrue($list->contains($items[0]));
        $this->assertFalse($list->contains($additional));

        $list->remove($additional);
        $this->assertEquals(2, $list->count());
        $this->assertTrue($list->contains($items[0]));
        $this->assertTrue($list->contains($items[1]));

        $list->add($additional);
        $this->assertEquals(3, $list->count());
        $this->assertTrue($list->contains($additional));

        $list->remove($items[0]);
        $this->assertEquals(2, $list->count());
        $this->assertFalse($list->contains($items[0]));

        $list->clear();
        $this->assertTrue($list->isEmpty());
        $this->assertEquals(0, $list->count());
        $this->assertEquals([], $list->toArray());
    }

    public function testListIsIterable()
    {
        $items = [
            new DefaultTargetProvider(),
            new DefaultTargetProvider(),
            new DefaultTargetProvider(),
        ];
        $list = new HandlersList(TargetProviderInterface::class, $items);
        $index = 0;
        foreach ($list as $item) {
            $this->assertSame($items[$index++], $item);
        }
    }

    public function testListItemsAreSortedByPriorityIfPossible()
    {
        $h1 = $this->prophesize(PrioritizedTypeProvider::class);
        $h1
            ->getPriority()
            ->shouldBeCalled()
            ->willReturn(0);
        $h2 = $this->prophesize(PrioritizedTypeProvider::class);
        $h2
            ->getPriority()
            ->shouldBeCalled()
            ->willReturn(10);

        $list = new HandlersList(PrioritizedTypeProvider::class, [
            $h1->reveal(),
            $h2->reveal(),
        ]);
        $expected = [
            $h2->reveal(),
            $h1->reveal(),
        ];
        $this->assertSame($expected, $list->toArray());

        $index = 0;
        foreach ($list as $item) {
            $this->assertSame($expected[$index++], $item);
        }
    }
}
