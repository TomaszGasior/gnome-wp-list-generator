<?php

/**
 * Implementation of enumeration inspired on SplEnum.
 */
class EnumClass
{
    protected const __default = null;

    private $value;

    public function __construct($value = null)
    {
        if (null === $value) {
            $this->value = static::__default;
            return;
        }

        if (in_array($value, $this->getConstList(), true)) {
            $this->value = $value;
        }
        throw new \Exception(sprintf('Value not a const in enum %s', static::class));
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getConstList(): array
    {
        return array_diff_key(
            (new ReflectionClass(static::class))->getConstants(),
            ['__default' => null]
        );
    }
}