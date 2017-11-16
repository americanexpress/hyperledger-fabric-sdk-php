<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\TestAsset;

use AmericanExpress\HyperledgerFabricClient\Options\AbstractOptions;

class FakeOptions extends AbstractOptions
{
    /**
     * @var string|null
     */
    private $fooBar;

    /**
     * @return string|null
     */
    public function getFooBar(): ?string
    {
        return $this->fooBar;
    }

    /**
     * @param string $fooBar
     */
    public function setFooBar(string $fooBar)
    {
        $this->fooBar = $fooBar;
    }
}
