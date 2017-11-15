<?php
declare(strict_types=1);

namespace AmericanExpressTest\FabricClient;

use AmericanExpress\HyperledgerFabricClient\ChaincodeInvocationSpecFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ChaincodeInvocationSpecFactory
 */
class ChaincodeInvocationSpecFactoryTest extends TestCase
{
    public function testCreateChaincodeInvocationSpec()
    {
        $result = ChaincodeInvocationSpecFactory::fromArgs([
            'foo',
            'bar',
        ]);

        self::assertSame(['foo', 'bar'], iterator_to_array($result->getChaincodeSpec()->getInput()->getArgs()));
    }
}
