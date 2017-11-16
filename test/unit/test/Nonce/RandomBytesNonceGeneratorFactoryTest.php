<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Nonce;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfig;
use AmericanExpress\HyperledgerFabricClient\Nonce\RandomBytesNonceGenerator;
use AmericanExpress\HyperledgerFabricClient\Nonce\RandomBytesNonceGeneratorFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Nonce\RandomBytesNonceGeneratorFactory
 */
class RandomBytesNonceGeneratorFactoryTest extends TestCase
{
    public function testFromConfig()
    {
        $result = RandomBytesNonceGeneratorFactory::fromConfig(new ClientConfig([]));

        self::assertInstanceOf(RandomBytesNonceGenerator::class, $result);
    }
}
