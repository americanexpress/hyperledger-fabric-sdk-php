<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Factory;

use AmericanExpress\HyperledgerFabricClient\Factory\ChaincodeInvocationSpecFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\SerializedIdentityFactory;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Factory\ProposalFactory
 */
class ProposalFactoryTest extends TestCase
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

        $header = HeaderFactory::create($serializedIdentity, $channelHeader, 'u58920du89f');

        $chaincodeInvocationSpec = ChaincodeInvocationSpecFactory::fromArgs([
            'foo',
            'bar',
        ]);

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpec(
            $chaincodeInvocationSpec
        );

        $result = ProposalFactory::create($header, $chaincodeProposalPayload);
        self::assertInstanceOf(Proposal::class, $result);

        $expectedHeader = '"MyChannelId*MyTransactionId:
ccPathccNamev12.34

FooBarFizBuzu58920du89f';

        $expectedPayload = '



foo
bar';

        self::assertContains($expectedHeader, $result->getHeader());
        self::assertSame($expectedPayload, $result->getPayload());
        self::assertSame('', $result->getExtension());
    }
}
