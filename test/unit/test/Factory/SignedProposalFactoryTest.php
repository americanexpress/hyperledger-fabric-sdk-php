<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Factory;

use AmericanExpress\HyperledgerFabricClient\Factory\ChaincodeInvocationSpecFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\SignedProposalFactory;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Factory\SignedProposalFactory
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
