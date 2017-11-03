<?php

namespace AmericanExpressTest\FabricClient\Utils;

use AmericanExpress\HyperledgerFabricClient\Utils;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Utils
 */
class UtilsTest extends TestCase
{
    /**
     * @var Utils
     */

    private $sut;

    protected function setUp()
    {
        $this->sut = new Utils();
    }

    public function testDefaultNonceLength()
    {
        $nonce = $this->sut->getNonce();

        self::assertSame(24, strlen($nonce));
    }
}
