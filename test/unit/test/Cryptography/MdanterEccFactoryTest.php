<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Security;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfig;
use AmericanExpress\HyperledgerFabricClient\Cryptography\MdanterEcc;
use AmericanExpress\HyperledgerFabricClient\Cryptography\MdanterEccFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Cryptography\MdanterEccFactory
 */
class MdanterEccFactoryTest extends TestCase
{
    public function testFromConfig()
    {
        $result = MdanterEccFactory::fromConfig(new ClientConfig([]));

        self::assertInstanceOf(MdanterEcc::class, $result);
    }
}
