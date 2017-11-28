<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Proposal;

use AmericanExpress\HyperledgerFabricClient\Proposal\Response;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Proposal\Response
 */
class ResponseTest extends TestCase
{
    public function testProposalResponse()
    {
        $response = new ProposalResponse();

        $sut = Response::fromProposalResponse($response);

        self::assertFalse($sut->isException());
        self::assertNull($sut->getException());
        self::assertSame($response, $sut->getProposalResponse());
    }

    public function testException()
    {
        $exception = new \Exception();

        $sut = Response::fromException($exception);

        self::assertTrue($sut->isException());
        self::assertSame($exception, $sut->getException());
        self::assertNull($sut->getProposalResponse());
    }
}
