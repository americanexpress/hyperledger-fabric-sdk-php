<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeInvocationSpecFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignedProposalFactory;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignedProposalFactory
 */
class SignedProposalFactoryTest extends TestCase
{
    public function testFromProposal()
    {
        $serializedIdentity = SerializedIdentityFactory::fromBytes('FooBar', 'FizBuz');

        $channelHeader = ChannelHeaderFactory::create(
            'MyTransactionId',
            'MyChannelId',
            'ccPath',
            'ccName',
            'v12.34'
        );

        $header = HeaderFactory::create($serializedIdentity, $channelHeader, 'u58920du89f');

        $chaincodeInvocationSpec = ChaincodeInvocationSpecFactory::fromArgs([
            'foo',
            'bar',
        ]);

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpec(
            $chaincodeInvocationSpec
        );

        $proposal = ProposalFactory::create($header, $chaincodeProposalPayload);

        $result = SignedProposalFactory::fromProposal($proposal, 'MySignature');

        self::assertInstanceOf(SignedProposal::class, $result);
        $expectedProposalBytes = '"MyChannelId*MyTransactionId:
ccPathccNamev12.34

FooBarFizBuzu58920du89f



foo
bar';
        self::assertContains($expectedProposalBytes, $result->getProposalBytes());
        self::assertSame('MySignature', $result->getSignature());
    }
}
