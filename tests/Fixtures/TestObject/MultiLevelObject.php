<?php

namespace Flying\ObjectBuilder\Tests\Fixtures\TestObject;

class MultiLevelObject
{
    /**
     * @var ChildObject
     */
    private $child;

    /**
     * @return ChildObject
     */
    public function getChild(): ChildObject
    {
        return $this->child;
    }

    /**
     * @param ChildObject $child
     */
    public function setChild(ChildObject $child): void
    {
        $this->child = $child;
    }
}
