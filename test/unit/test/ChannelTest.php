<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\ChaincodeQueryParams;
use AmericanExpress\HyperledgerFabricClient\Channel;
use AmericanExpress\HyperledgerFabricClient\ChannelContext;
use AmericanExpress\HyperledgerFabricClient\EndorserClientManagerInterface;
use AmericanExpress\HyperledgerFabricClient\Signatory\SignatoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactoryInterface;
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
     * @var SignatoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $signatory;

    /**
     * @var EndorserClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private $endorserClient;

    /**
     * @var TransactionContextFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionContextFactory;

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

        $this->transactionContextFactory = self::getMockBuilder(TransactionContextFactoryInterface::class)
            ->getMock();

        $this->signatory = self::getMockBuilder(SignatoryInterface::class)
            ->getMock();

        $this->unaryCall = self::getMockBuilder(UnaryCall::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new Channel($endorserClients, $this->transactionContextFactory, $this->signatory);
    }

    public function testQueryByChaincode()
    {
        $file = new \SplFileObject(__FILE__);

        $context = new ChannelContext([
            'host' => 'example.com',
            'mspId' => '1234',
            'adminCerts' => $file,
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

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException
     * @expectedExceptionMessage Connect failed
     * @expectedExceptionCode 14
     */
    public function testQueryByChaincodeConnectionFailure()
    {
        $file = new \SplFileObject(__FILE__);

        $context = new ChannelContext([
            'host' => 'example.com',
            'mspId' => '1234',
            'adminCerts' => $file,
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

        $this->unaryCall->method('wait')
            ->willReturn([
                null,
                [
                    'code' => 14,
                    'details' => 'Connect failed',
                    'metadata' => [],
                ]
            ]);

        $this->sut->queryByChainCode($context, $params);
    }
}
