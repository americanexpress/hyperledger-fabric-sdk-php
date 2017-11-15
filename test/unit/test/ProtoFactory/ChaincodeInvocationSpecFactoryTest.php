<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeInvocationSpecFactory;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInput;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInvocationSpec;
use Hyperledger\Fabric\Protos\Peer\ChaincodeSpec;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeInvocationSpecFactory
 */
class ChaincodeInvocationSpecFactoryTest extends TestCase
{
    public function testCreateChaincodeInvocationSpec()
    {
        $result = ChaincodeInvocationSpecFactory::fromArgs([
            'foo',
            'bar',
        ]);

        self::assertInstanceOf(ChaincodeInvocationSpec::class, $result);

        $chaincodeSpec = $result->getChaincodeSpec();
        self::assertInstanceOf(ChaincodeSpec::class, $chaincodeSpec);

        $chaincodeInput = $chaincodeSpec->getInput();
        self::assertInstanceOf(ChaincodeInput::class, $chaincodeInput);
        self::assertSame(['foo', 'bar'], iterator_to_array($chaincodeInput->getArgs()));
    }
}
