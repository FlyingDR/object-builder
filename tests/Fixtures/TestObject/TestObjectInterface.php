<?php

namespace Flying\ObjectBuilder\Tests\Fixtures\TestObject;

/**
 * Interface for test objects for object builder
 */
interface TestObjectInterface
{
    /**
     * Get data to build this object with
     *
     * @return array
     */
    public function getBuildData(): array;

    /**
     * Get data that is expected to be in object after building
     *
     * @return array
     */
    public function getExpectedResult(): array;

    /**
     * Get data that is actually available in object after building
     *
     * @return array
     */
    public function getActualResult(): array;
}
