<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Proposal;

use AmericanExpress\HyperledgerFabricClient\Proposal\Response;
use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection
 */
class ResponseCollectionTest extends TestCase
{
    public function testGetDefaults()
    {
        $sut = new ResponseCollection();

        self::assertFalse($sut->hasProposalResponses());
        self::assertCount(0, $sut->getProposalResponses());

        self::assertFalse($sut->hasExceptions());
        self::assertCount(0, $sut->getExceptions());
    }

    public function testGetResponses()
    {
        $sut = new ResponseCollection([
            Response::fromException(new \Exception()),
            Response::fromProposalResponse(new ProposalResponse()),
            Response::fromException(new \Exception()),
            Response::fromProposalResponse(new ProposalResponse()),
        ]);

        self::assertTrue($sut->hasProposalResponses());
        self::assertCount(2, $sut->getProposalResponses());

        self::assertTrue($sut->hasExceptions());
        self::assertCount(2, $sut->getExceptions());
    }
}
