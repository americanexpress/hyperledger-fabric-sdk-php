<?php
declare(strict_types=1);

namespace AmericanExpressTest\FabricClient;

use AmericanExpress\HyperledgerFabricClient\ClientConfig;
use AmericanExpress\HyperledgerFabricClient\Hash;
use AmericanExpress\HyperledgerFabricClient\Utils;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Hash
 */
class HashTest extends TestCase
{
    /**
     * @var Hash
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new Hash(new ClientConfig([]));
    }

    public function testDefaultNonceLength()
    {
        $nonce = $this->sut->getNonce();

        self::assertSame(24, strlen($nonce));
    }

    public function testConfigurableNonceLength()
    {
        $nonce = (new Hash(new ClientConfig(['nonce-size' => 3])))->getNonce();

        self::assertSame(3, strlen($nonce));
    }

    public function testGetByteArray()
    {
        $actual = $this->sut->toByteArray('FooBar');

        $expected = [
            1 => 70,
            2 => 111,
            3 => 111,
            4 => 66,
            5 => 97,
            6 => 114,
        ];

        self::assertSame($expected, $actual);
    }

    public function testProposalArrayToBinaryString()
    {
        $result = $this->sut->proposalArrayToBinaryString([
            'foo',
            'bar',
        ]);

        self::assertNotEmpty($result);
    }
}
