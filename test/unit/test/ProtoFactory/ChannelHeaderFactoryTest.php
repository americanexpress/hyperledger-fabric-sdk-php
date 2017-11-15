<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use Google\Protobuf\Timestamp;
use Hyperledger\Fabric\Protos\Common\ChannelHeader;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory
 */
class ChannelHeaderFactoryTest extends TestCase
{
    public function testDefaultCreate()
    {
        $result = ChannelHeaderFactory::create(
            'MyTransactionId',
            'MyChannelId',
            'FooBar',
            'FizBuz',
            'v12.34'
        );

        self::assertInstanceOf(ChannelHeader::class, $result);
        self::assertSame(3, $result->getType());
        self::assertSame(1, $result->getVersion());
        self::assertInstanceOf(Timestamp::class, $result->getTimestamp());
        self::assertSame('MyChannelId', $result->getChannelId());
        self::assertSame('MyTransactionId', $result->getTxId());
        self::assertSame(0, $result->getEpoch());
        $expected = <<<'TAG'

FooBarFizBuzv12.34
TAG;
        self::assertSame($expected, $result->getExtension());
    }
}
