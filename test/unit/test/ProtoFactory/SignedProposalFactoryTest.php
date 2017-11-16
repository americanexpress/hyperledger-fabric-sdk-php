<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignedProposalFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignedProposalFactory
 */
class SignedProposalFactoryTest extends TestCase
{
    public function testFromProposal()
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

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs([
            'foo',
            'bar',
        ]);

        $proposal = ProposalFactory::create($header, $chaincodeProposalPayload);

        $result = SignedProposalFactory::fromProposal($proposal, 'MySignature');

        self::assertInstanceOf(SignedProposal::class, $result);
        self::assertContains('Alice', $result->getProposalBytes());
        self::assertContains('Bob', $result->getProposalBytes());
        self::assertContains('MyChannelId', $result->getProposalBytes());
        self::assertContains('MyTransactionId', $result->getProposalBytes());
        self::assertContains('FooBar', $result->getProposalBytes());
        self::assertContains('FizBuz', $result->getProposalBytes());
        self::assertContains('v12.34', $result->getProposalBytes());
        self::assertContains('u58920du89f', $result->getProposalBytes());
        self::assertContains('foo', $result->getProposalBytes());
        self::assertContains('bar', $result->getProposalBytes());
        self::assertSame('MySignature', $result->getSignature());
    }
}
