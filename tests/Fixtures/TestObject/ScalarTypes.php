<?php

namespace Flying\ObjectBuilder\Tests\Fixtures\TestObject;

class ScalarTypes implements TestObjectInterface
{
    /**
     * @var mixed
     */
    protected $mixed;
    /**
     * @var mixed|null
     */
    protected $nullableMixed;
    /**
     * @var boolean
     */
    protected $bool;
    /**
     * @var boolean
     */
    protected $boolean;
    /**
     * @var boolean|null
     */
    protected $nullableBool;
    /**
     * @var int
     */
    protected $int;
    /**
     * @var int
     */
    protected $integer;
    /**
     * @var int|null
     */
    protected $nullableInt;
    /**
     * @var float
     */
    protected $float;
    /**
     * @var float|null
     */
    protected $nullableFloat;
    /**
     * @var double
     */
    protected $double;
    /**
     * @var double|null
     */
    protected $nullableDouble;
    /**
     * @var string
     */
    protected $string;
    /**
     * @var string|null
     */
    protected $nullableString;

    /**
     * @return mixed
     */
    public function getMixed()
    {
        return $this->mixed;
    }

    /**
     * @param mixed $mixed
     */
    public function setMixed($mixed): void
    {
        $this->mixed = $mixed;
    }

    /**
     * @return mixed|null
     */
    public function getNullableMixed(): ?mixed
    {
        return $this->nullableMixed;
    }

    /**
     * @param mixed|null $nullableMixed
     */
    public function setNullableMixed(?mixed $nullableMixed): void
    {
        $this->nullableMixed = $nullableMixed;
    }

    /**
     * @return bool
     */
    public function isBool(): bool
    {
        return $this->bool;
    }

    /**
     * @param bool $bool
     */
    public function setBool(bool $bool): void
    {
        $this->bool = $bool;
    }

    /**
     * @return bool
     */
    public function isBoolean(): bool
    {
        return $this->boolean;
    }

    /**
     * @param bool $boolean
     */
    public function setBoolean(bool $boolean): void
    {
        $this->boolean = $boolean;
    }

    /**
     * @return bool|null
     */
    public function getNullableBool(): ?bool
    {
        return $this->nullableBool;
    }

    /**
     * @param bool|null $nullableBool
     */
    public function setNullableBool(?bool $nullableBool): void
    {
        $this->nullableBool = $nullableBool;
    }

    /**
     * @return int
     */
    public function getInt(): int
    {
        return $this->int;
    }

    /**
     * @param int $int
     */
    public function setInt(int $int): void
    {
        $this->int = $int;
    }

    /**
     * @return int
     */
    public function getInteger(): int
    {
        return $this->integer;
    }

    /**
     * @param int $integer
     */
    public function setInteger(int $integer): void
    {
        $this->integer = $integer;
    }

    /**
     * @return int|null
     */
    public function getNullableInt(): ?int
    {
        return $this->nullableInt;
    }

    /**
     * @param int|null $nullableInt
     */
    public function setNullableInt(?int $nullableInt): void
    {
        $this->nullableInt = $nullableInt;
    }

    /**
     * @return float
     */
    public function getFloat(): float
    {
        return $this->float;
    }

    /**
     * @param float $float
     */
    public function setFloat(float $float): void
    {
        $this->float = $float;
    }

    /**
     * @return float|null
     */
    public function getNullableFloat(): ?float
    {
        return $this->nullableFloat;
    }

    /**
     * @param float|null $nullableFloat
     */
    public function setNullableFloat(?float $nullableFloat): void
    {
        $this->nullableFloat = $nullableFloat;
    }

    /**
     * @return float
     */
    public function getDouble(): float
    {
        return $this->double;
    }

    /**
     * @param float $double
     */
    public function setDouble(float $double): void
    {
        $this->double = $double;
    }

    /**
     * @return float|null
     */
    public function getNullableDouble(): ?float
    {
        return $this->nullableDouble;
    }

    /**
     * @param float|null $nullableDouble
     */
    public function setNullableDouble(?float $nullableDouble): void
    {
        $this->nullableDouble = $nullableDouble;
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }

    /**
     * @param string $string
     */
    public function setString(string $string): void
    {
        $this->string = $string;
    }

    /**
     * @return null|string
     */
    public function getNullableString(): ?string
    {
        return $this->nullableString;
    }

    /**
     * @param null|string $nullableString
     */
    public function setNullableString(?string $nullableString): void
    {
        $this->nullableString = $nullableString;
    }

    /**
     * Get data to build this object with
     *
     * @return array
     */
    public function getBuildData(): array
    {
        return [
            'mixed'          => null,
            'nullableMixed'  => null,
            'bool'           => true,
            'boolean'        => false,
            'nullableBool'   => null,
            'int'            => 123,
            'integer'        => -12345,
            'nullableInt'    => null,
            'float'          => 123.45,
            'nullableFloat'  => null,
            'double'         => -12345.67,
            'nullableDouble' => null,
            'string'         => 'abc',
            'nullableString' => null,
        ];
    }

    /**
     * Get data that is expected to be in object after building
     *
     * @return array
     */
    public function getExpectedResult(): array
    {
        return [
            'mixed'          => null,
            'nullableMixed'  => null,
            'bool'           => true,
            'boolean'        => false,
            'nullableBool'   => null,
            'int'            => 123,
            'integer'        => -12345,
            'nullableInt'    => null,
            'float'          => 123.45,
            'nullableFloat'  => null,
            'double'         => -12345.67,
            'nullableDouble' => null,
            'string'         => 'abc',
            'nullableString' => null,
        ];
    }

    /**
     * Get data that is actually available in object after building
     *
     * @return array
     */
    public function getActualResult(): array
    {
        return [
            'mixed'          => $this->mixed,
            'nullableMixed'  => $this->nullableMixed,
            'bool'           => $this->bool,
            'boolean'        => $this->boolean,
            'nullableBool'   => $this->nullableBool,
            'int'            => $this->int,
            'integer'        => $this->integer,
            'nullableInt'    => $this->nullableInt,
            'float'          => $this->float,
            'nullableFloat'  => $this->nullableFloat,
            'double'         => $this->double,
            'nullableDouble' => $this->nullableDouble,
            'string'         => $this->string,
            'nullableString' => $this->nullableString,
        ];
    }
}
