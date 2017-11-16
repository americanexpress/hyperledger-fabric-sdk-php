<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext;
use Hyperledger\Fabric\Protos\Common\Header;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory
 */
class HeaderFactoryTest extends TestCase
{
    public function testCreate()
    {
        $channelHeader = ChannelHeaderFactory::create(
            new TransactionContext(
                $serializedIdentity = SerializedIdentityFactory::fromBytes('Alice', 'Bob'),
                $nonce = 'u58920du89f',
                'MyTransactionId'
            ),
            'MyChannelId',
            'FooBar',
            'FizBuz',
            'v12.34'
        );

        $result = HeaderFactory::create($serializedIdentity, $channelHeader, $nonce);

        self::assertInstanceOf(Header::class, $result);
        self::assertContains('MyChannelId', $result->getChannelHeader());
        self::assertContains('MyTransactionId', $result->getChannelHeader());
        self::assertContains('FooBar', $result->getChannelHeader());
        self::assertContains('FizBuz', $result->getChannelHeader());
        self::assertContains('v12.34', $result->getChannelHeader());
        self::assertContains('Alice', $result->getSignatureHeader());
        self::assertContains('Bob', $result->getSignatureHeader());
        self::assertContains('u58920du89f', $result->getSignatureHeader());
    }

    public function testFromTransactionContext()
    {
        $channelHeader = ChannelHeaderFactory::create(
            $transactionContext = new TransactionContext(
                SerializedIdentityFactory::fromBytes('Alice', 'Bob'),
                'u58920du89f',
                'MyTransactionId'
            ),
            'MyChannelId',
            'FooBar',
            'FizBuz',
            'v12.34'
        );

        $result = HeaderFactory::fromTransactionContext($transactionContext, $channelHeader);

        self::assertInstanceOf(Header::class, $result);
        self::assertContains('MyChannelId', $result->getChannelHeader());
        self::assertContains('MyTransactionId', $result->getChannelHeader());
        self::assertContains('FooBar', $result->getChannelHeader());
        self::assertContains('FizBuz', $result->getChannelHeader());
        self::assertContains('v12.34', $result->getChannelHeader());
        self::assertContains('Alice', $result->getSignatureHeader());
        self::assertContains('Bob', $result->getSignatureHeader());
        self::assertContains('u58920du89f', $result->getSignatureHeader());
    }
}
