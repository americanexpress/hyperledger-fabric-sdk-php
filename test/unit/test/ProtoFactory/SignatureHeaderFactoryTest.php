<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignatureHeaderFactory;
use Hyperledger\Fabric\Protos\Common\SignatureHeader;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignatureHeaderFactory
 */
class SignatureHeaderFactoryTest extends TestCase
{
    public function testCreate()
    {
        $serializedIdentity = SerializedIdentityFactory::fromBytes('FooBar', 'FizBuz');

        $result = SignatureHeaderFactory::create($serializedIdentity, '78erw87vxj7842jf');

        self::assertInstanceOf(SignatureHeader::class, $result);
        self::assertSame("\nFooBarFizBuz", $result->getCreator());
        self::assertSame('78erw87vxj7842jf', $result->getNonce());
    }
}
