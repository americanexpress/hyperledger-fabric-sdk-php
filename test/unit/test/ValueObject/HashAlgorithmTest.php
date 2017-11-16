<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ValueObject;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfig;
use AmericanExpress\HyperledgerFabricClient\ValueObject\HashAlgorithm;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ValueObject\HashAlgorithm
 */
class HashAlgorithmTest extends TestCase
{
    public function testDefaultHashAlgorithm()
    {
        $sut = new HashAlgorithm();

        self::assertSame('sha256', (string) $sut);
    }

    public function testValidHashAlgorithm()
    {
        $sut = new HashAlgorithm('sha512');

        self::assertSame('sha512', (string) $sut);
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testInvalidHashAlgorithm()
    {
        new HashAlgorithm('InvalidAlgorithm');
    }

    public function testFromConfig()
    {
        $result = HashAlgorithm::fromConfig(new ClientConfig([
            'crypto-hash-algo' => 'sha512',
        ]));

        self::assertSame('sha512', (string) $result);
    }
}
