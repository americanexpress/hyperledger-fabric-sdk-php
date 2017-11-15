<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Factory;

use AmericanExpress\HyperledgerFabricClient\Factory\ChaincodeInputFactory;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInput;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Factory\ChaincodeInputFactory
 */
class ChaincodeInputFactoryTest extends TestCase
{
    public function testFromArgs()
    {
        $result = ChaincodeInputFactory::fromArgs([
            'foo',
            'bar',
        ]);

        self::assertInstanceOf(ChaincodeInput::class, $result);
        self::assertSame(['foo', 'bar'], iterator_to_array($result->getArgs()));
    }
}
