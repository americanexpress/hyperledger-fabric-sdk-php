<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext;
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
            new TransactionContext(
                SerializedIdentityFactory::fromBytes('Alice', 'Bob'),
                'u58920du89f',
                'MyTransactionId'
            ),
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
        self::assertContains('FooBar', $result->getExtension());
        self::assertContains('FizBuz', $result->getExtension());
        self::assertContains('v12.34', $result->getExtension());
    }
}
