<?php

namespace Flying\ObjectBuilder\Tests\Handler\TargetProvider;

use Flying\ObjectBuilder\Handler\TargetProvider\DefaultTargetProvider;
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
        $reflection = new \ReflectionClass(\stdClass::class);
        $provider = new DefaultTargetProvider();
        $this->assertEquals($expected, $provider->getTarget($reflection, $name));
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
