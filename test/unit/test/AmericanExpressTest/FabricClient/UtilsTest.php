<?php
declare(strict_types=1);

namespace AmericanExpressTest\FabricClient;

use AmericanExpress\HyperledgerFabricClient\ClientConfig;
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
        $this->sut = new Utils(new ClientConfig([]));
    }

    public function testDefaultNonceLength()
    {
        $nonce = $this->sut->getNonce();

        self::assertSame(24, strlen($nonce));
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

    public function testFabricConnect()
    {
        self::markTestIncomplete('Inject config to make this function testable.');
    }

    public function testCreateChaincodeInvocationSpec()
    {
        $result = $this->sut->createChaincodeInvocationSpec([
            'foo',
            'bar',
        ]);

        self::assertSame(['foo', 'bar'], iterator_to_array($result->getChaincodeSpec()->getInput()->getArgs()));
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
