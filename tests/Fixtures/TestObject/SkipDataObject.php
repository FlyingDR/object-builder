<?php

namespace Flying\ObjectBuilder\Tests\Fixtures\TestObject;

class SkipDataObject
{
    /**
     * @var string
     */
    private $value;
    /**
     * @var string
     */
    private $skipped;

    /**
     * @return string
     */
    public function getValue(): ?string
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
     * @return string
     */
    public function getSkipped(): ?string
    {
        return $this->skipped;
    }

    /**
     * @param string $skipped
     */
    public function setSkipped(string $skipped): void
    {
        $this->skipped = $skipped;
    }
}
