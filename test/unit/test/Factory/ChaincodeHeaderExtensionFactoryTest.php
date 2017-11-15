<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Factory;

use AmericanExpress\HyperledgerFabricClient\Factory\ChaincodeHeaderExtensionFactory;
use Hyperledger\Fabric\Protos\Peer\ChaincodeHeaderExtension;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Factory\ChaincodeHeaderExtensionFactory
 */
class ChaincodeHeaderExtensionFactoryTest extends TestCase
{
    public function testFromChaincodeId()
    {
        $chaincodeId = new ChaincodeID();

        $result = ChaincodeHeaderExtensionFactory::fromChaincodeId($chaincodeId);

        self::assertInstanceOf(ChaincodeHeaderExtension::class, $result);
        self::assertSame('', $result->getPayloadVisibility());
        self::assertSame($chaincodeId, $result->getChaincodeId());
    }
}
