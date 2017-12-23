<?php

namespace Flying\ObjectBuilder\Tests\Handler\TypeConverter\Fixtures;

class ParentObject
{
    /**
     * @var Child
     */
    private $child;

    /**
     * @return Child|null
     */
    public function getChild(): ?Child
    {
        return $this->child;
    }

    /**
     * @param Child $child
     */
    public function setChildByNormalClass(Child $child): void
    {
        $this->child = $child;
    }

    /**
     * @param AbstractChild $child
     */
    public function setChildByAbstractClass(AbstractChild $child): void
    {
        $this->child = $child;
    }

    /**
     * @param ChildInterface $child
     */
    public function setChildByInterface(ChildInterface $child): void
    {
        $this->child = $child;
    }

    public function setterWithGenericArgument($arg): void
    {

    }

    public function setterWithNonObjectArgument(string $arg): void
    {

    }

    public function setterWithMultipleArguments($a, $b, $c): void
    {

    }
}
