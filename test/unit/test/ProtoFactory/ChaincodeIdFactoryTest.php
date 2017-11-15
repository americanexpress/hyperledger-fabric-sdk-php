<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeIdFactory;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;
use PHPUnit\Framework\TestCase;

class ChaincodeIdFactoryTest extends TestCase
{
    public function testCreate()
    {
        $result = ChaincodeIdFactory::create('FooBar', 'FizBuz', 'v12.34');

        self::assertInstanceOf(ChaincodeId::class, $result);
        self::assertSame('FooBar', $result->getPath());
        self::assertSame('FizBuz', $result->getName());
        self::assertSame('v12.34', $result->getVersion());
    }
}
