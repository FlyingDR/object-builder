<?php

namespace Flying\ObjectBuilder\Tests\Handler\TargetProvider;

use Flying\ObjectBuilder\Handler\TargetProvider\DefaultTargetProvider;
use Flying\ObjectBuilder\Tests\Handler\TargetProvider\Fixtures\TestMethodNamesTransformation;
use PHPUnit\Framework\TestCase;

class DefaultTargetProviderTest extends TestCase
{
    /**
     * @param string $name
     * @param string $expected
     * @dataProvider dpNames
     */
    public function testTargetProviding(string $name, string $expected)
    {
        $reflection = new \ReflectionClass(TestMethodNamesTransformation::class);
        $provider = new DefaultTargetProvider();
        /** @var \ReflectionMethod $method */
        $method = $provider->getTarget($reflection, $name);
        $this->assertInstanceOf(\ReflectionMethod::class, $method);
        /** @noinspection NullPointerExceptionInspection */
        $this->assertEquals($expected, $method->name);
    }

    public function dpNames()
    {
        return [
            ['simple', 'setSimple'],
            ['dash-test', 'setDashTest'],
            ['underscore_test', 'setUnderscoreTest'],
            ['_head_underscore', 'setHeadUnderscore'],
            ['tail_underscore_', 'setTailUnderscore'],
            ['multiple___underscores', 'setMultipleUnderscores'],
            ['multiple---dashes', 'setMultipleDashes'],
            ['snakeCase', 'setSnakeCase'],
            ['CamelCase', 'setCamelCase'],
        ];
    }
}
