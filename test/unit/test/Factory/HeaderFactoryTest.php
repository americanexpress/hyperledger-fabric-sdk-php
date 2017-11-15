<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Factory;

use AmericanExpress\HyperledgerFabricClient\Factory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\SerializedIdentityFactory;
use Hyperledger\Fabric\Protos\Common\Header;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Factory\HeaderFactory
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
