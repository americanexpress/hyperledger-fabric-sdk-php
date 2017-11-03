<?php

namespace AmericanExpressTest\FabricClient\Utils;

use AmericanExpress\FabricClient\Utils;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\FabricClient\Utils
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
