<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Nonce;

use AmericanExpress\HyperledgerFabricClient\Nonce\RandomBytesNonceGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Nonce\RandomBytesNonceGenerator
 */
class RandomBytesNonceGeneratorTest extends TestCase
{
    /**
     * @var RandomBytesNonceGenerator
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new RandomBytesNonceGenerator();
    }

    public function testDefaultNonceLength()
    {
        $nonce = $this->sut->generateNonce();

        self::assertSame(24, strlen($nonce));
    }

    public function testConfigurableNonceLength()
    {
        $nonce = (new RandomBytesNonceGenerator(3))->generateNonce();

        self::assertSame(3, strlen($nonce));
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testInvalidNonceSize()
    {
        new RandomBytesNonceGenerator(-1);
    }
}
