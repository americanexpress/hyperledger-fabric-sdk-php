<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use Hyperledger\Fabric\Protos\Common\Header;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory
 */
class HeaderFactoryTest extends TestCase
{
    public function testCreate()
    {
        $serializedIdentity = SerializedIdentityFactory::fromBytes('FooBar', 'FizBuz');

        $channelHeader = ChannelHeaderFactory::create(
            'MyTransactionId',
            'MyChannelId',
            'ccPath',
            'ccName',
            'v12.34'
        );

        $result = HeaderFactory::create($serializedIdentity, $channelHeader, 'u58920du89f');

        self::assertInstanceOf(Header::class, $result);

        $expectedChannelHeader = <<<'TAG'
"MyChannelId*MyTransactionId:
ccPathccNamev12.34
TAG;

        $expectedSignatureHeader = <<<'TAG'


FooBarFizBuzu58920du89f
TAG;

        self::assertContains($expectedChannelHeader, $result->getChannelHeader());
        self::assertSame($expectedSignatureHeader, $result->getSignatureHeader());
    }
}
