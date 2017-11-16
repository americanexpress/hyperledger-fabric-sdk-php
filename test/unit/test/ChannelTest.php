<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\ChaincodeQueryParams;
use AmericanExpress\HyperledgerFabricClient\Channel;
use AmericanExpress\HyperledgerFabricClient\ChannelContext;
use AmericanExpress\HyperledgerFabricClient\Cryptography\CryptographyInterface;
use AmericanExpress\HyperledgerFabricClient\EndorserClientManagerInterface;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Channel
 */
class ChannelTest extends TestCase
{
    /**
     * @var UnaryCall|\PHPUnit_Framework_MockObject_MockObject
     */
    private $unaryCall;

    /**
     * @var EndorserClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private $endorserClient;

    /**
     * @var CryptographyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cryptography;

    /**
     * @var Channel
     */
    private $sut;

    protected function setUp()
    {
        $this->endorserClient = self::getMockBuilder(EndorserClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var EndorserClientManagerInterface|\PHPUnit_Framework_MockObject_MockObject $endorserClients */
        $endorserClients = self::getMockBuilder(EndorserClientManagerInterface::class)
            ->getMock();

        $endorserClients->method('get')
            ->willReturn($this->endorserClient);

        $this->cryptography = self::getMockBuilder(CryptographyInterface::class)
            ->getMock();

        $this->unaryCall = self::getMockBuilder(UnaryCall::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new Channel($endorserClients, $this->cryptography);
    }

    public function testQueryByChaincode()
    {
        $file = new \SplFileObject(__FILE__);

        $context = new ChannelContext([
            'host' => 'example.com',
            'mspId' => '1234',
            'adminCerts' => $file,
            'epoch' => 54321,
            'privateKey' => $file,
        ]);

        $params = new ChaincodeQueryParams([
            'channelId' => 'MyChannelId',
            'chaincodeName' => 'FooBar',
            'chaincodePath' => 'FizBuz',
            'chaincodeVersion' => 'v12.34',
            'args' => [
                'foo' => 'bar',
            ],
        ]);

        $this->endorserClient->method('ProcessProposal')
            ->willReturn($this->unaryCall);

        $proposalResponse = new ProposalResponse();

        $this->unaryCall->method('wait')
            ->willReturn([
                $proposalResponse,
                [
                    'code' => 0,
                ]
            ]);

        $result = $this->sut->queryByChainCode($context, $params);

        self::assertSame($proposalResponse, $result);
    }
}
