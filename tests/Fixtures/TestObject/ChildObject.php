<?php

namespace Flying\ObjectBuilder\Tests\Fixtures\TestObject;

class ChildObject
{
    /**
     * @var string
     */
    private $value;
    /**
     * @var ChildObject
     */
    private $child;

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

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
