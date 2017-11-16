<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\ValueObject;

class StringValue
{
    /**
     * @var string
     */
    private $value;

    /**
     * StringValue constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
