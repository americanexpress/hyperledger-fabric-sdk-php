<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeInvocationSpecFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory
 */
class ProposalFactoryTest extends TestCase
{
    public function testCreate()
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

        $header = HeaderFactory::fromTransactionContext($transactionContext, $channelHeader);

        $chaincodeInvocationSpec = ChaincodeInvocationSpecFactory::fromArgs([
            'foo',
            'bar',
        ]);

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpec(
            $chaincodeInvocationSpec
        );

        $result = ProposalFactory::create($header, $chaincodeProposalPayload);
        self::assertInstanceOf(Proposal::class, $result);
        self::assertContains('Alice', $result->getHeader());
        self::assertContains('Bob', $result->getHeader());
        self::assertContains('MyChannelId', $result->getHeader());
        self::assertContains('MyTransactionId', $result->getHeader());
        self::assertContains('FooBar', $result->getHeader());
        self::assertContains('FizBuz', $result->getHeader());
        self::assertContains('v12.34', $result->getHeader());
        self::assertContains('u58920du89f', $result->getHeader());
        self::assertContains('foo', $result->getPayload());
        self::assertContains('bar', $result->getPayload());
        self::assertSame('', $result->getExtension());
    }
}
