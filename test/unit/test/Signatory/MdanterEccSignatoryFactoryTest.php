<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Signatory;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfig;
use AmericanExpress\HyperledgerFabricClient\Signatory\MdanterEccSignatory;
use AmericanExpress\HyperledgerFabricClient\Signatory\MdanterEccSignatoryFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Signatory\MdanterEccSignatoryFactory
 */
class MdanterEccSignatoryFactoryTest extends TestCase
{
    public function testFromConfig()
    {
        $result = MdanterEccSignatoryFactory::fromConfig(new ClientConfig([]));

        self::assertInstanceOf(MdanterEccSignatory::class, $result);
    }
}
