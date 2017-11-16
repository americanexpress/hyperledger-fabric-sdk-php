<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Signatory;

use AmericanExpress\HyperledgerFabricClient\ChannelContext;
use AmericanExpress\HyperledgerFabricClient\Nonce\NonceGeneratorInterface;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\TimestampFactory;
use AmericanExpress\HyperledgerFabricClient\Signatory\MdanterEccSignatory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TxIdFactory;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Signatory\MdanterEccSignatory
 */
class MdanterEccSignatoryTest extends TestCase
{
    /**
     * @var vfsStreamFile
     */
    private $privateKey;

    /**
     * @var MdanterEccSignatory
     */
    private $sut;

    protected function setUp()
    {
        $files = vfsStream::setup('test');

        $this->privateKey = vfsStream::newFile('foo');
        $this->privateKey->setContent(<<<'TAG'
-----BEGIN PRIVATE KEY-----
MIGHAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBG0wawIBAQQghnA7rdgbZi/wndus
iXjyf0KgE6OKZjQ+5INjwelRAC6hRANCAASb3u+hY+U/FZvhYDN6d08HJ1v56UJU
yz/n2NHyJgTg6kC05AaJMeGIinEF0JeJtRDNVQGzoQJQYjnzUTS9FvGh
-----END PRIVATE KEY-----
TAG
        );
        $files->addChild($this->privateKey);

        $this->sut = new MdanterEccSignatory();
    }

    public function testSignProposal()
    {
        $result = $this->sut->signProposal(new Proposal(), new \SplFileObject($this->privateKey->url()));

        self::assertInstanceOf(SignedProposal::class, $result);
        self::assertInternalType('string', $result->getProposalBytes());
        self::assertEmpty($result->getProposalBytes());
        self::assertInternalType('string', $result->getSignature());
        self::assertNotEmpty($result->getSignature());
    }

    /**
     * @covers       \AmericanExpress\HyperledgerFabricClient\Signatory\MdanterEccSignatory::getS
     * @dataProvider dataGetS
     * @param \DateTime $dateTime
     * @param string $encodedProposalBytes
     * @param string $encodedSignature
     */
    public function testGetS(\DateTime $dateTime, string $encodedProposalBytes, string $encodedSignature)
    {
        $transactionContextFactory = new TransactionContextFactory(
            new class implements NonceGeneratorInterface {
                public function generateNonce(): string
                {
                    return 'u23m5k4hf86j';
                }
            },
            new TxIdFactory()
        );
        $channelContext = new ChannelContext([
            'mspId' => '1234',
            'adminCerts' => new \SplFileObject($this->privateKey->url()),
        ]);
        $transactionContext = $transactionContextFactory->fromChannelContext($channelContext);
        $channelHeader = ChannelHeaderFactory::create(
            $transactionContext,
            'MyChannelId',
            'MyChaincodePath',
            'MyChaincodeName',
            'MyChaincodeVersion',
            3,
            1,
            TimestampFactory::fromDateTime($dateTime)
        );
        $header = HeaderFactory::fromTransactionContext($transactionContext, $channelHeader);
        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs([]);
        $proposal = ProposalFactory::create($header, $chaincodeProposalPayload);

        $result = $this->sut->signProposal($proposal, new \SplFileObject($this->privateKey->url()));

        self::assertInstanceOf(SignedProposal::class, $result);
        self::assertEquals($encodedProposalBytes, base64_encode($result->getProposalBytes()));
        self::assertEquals($encodedSignature, base64_encode($result->getSignature()));
    }

    public function dataGetS()
    {
        return [
            [
                new \DateTime('2017-11-16T11:00:00Z'),
                'CqUDCpUBCAMQARoGCLDftdAFIgtNeUNoYW5uZWxJZCpANTg0YWU3YzczMTJkMTRmNjA5MWI2ZjA1ZmZkYmQ1ZDA5NTA1OGE0MGMyMDQ2NmY3OGQ4ZWZlMjE4NmNkM2VmMTo4EjYKD015Q2hhaW5jb2RlUGF0aBIPTXlDaGFpbmNvZGVOYW1lGhJNeUNoYWluY29kZVZlcnNpb24SigIK+QEKBDEyMzQS8AEtLS0tLUJFR0lOIFBSSVZBVEUgS0VZLS0tLS0KTUlHSEFnRUFNQk1HQnlxR1NNNDlBZ0VHQ0NxR1NNNDlBd0VIQkcwd2F3SUJBUVFnaG5BN3JkZ2JaaS93bmR1cwppWGp5ZjBLZ0U2T0taalErNUlOandlbFJBQzZoUkFOQ0FBU2IzdStoWStVL0ZadmhZRE42ZDA4SEoxdjU2VUpVCnl6L24yTkh5SmdUZzZrQzA1QWFKTWVHSWluRUYwSmVKdFJETlZRR3pvUUpRWWpuelVUUzlGdkdoCi0tLS0tRU5EIFBSSVZBVEUgS0VZLS0tLS0SDHUyM201azRoZjg2ahIGCgQKAhoA',
                'MEQCIC7Rv6xBiXDybRH7owipe5aB5eMWBCYdEOkF3/u6SJ2FAiBCaR89Rae61+VyddJZQkn8GTGg2lz9Dgr4xvW95eLdIg==',
            ],
            [
                new \DateTime('2017-11-16T00:00:00Z'),
                'CqUDCpUBCAMQARoGCICqs9AFIgtNeUNoYW5uZWxJZCpANTg0YWU3YzczMTJkMTRmNjA5MWI2ZjA1ZmZkYmQ1ZDA5NTA1OGE0MGMyMDQ2NmY3OGQ4ZWZlMjE4NmNkM2VmMTo4EjYKD015Q2hhaW5jb2RlUGF0aBIPTXlDaGFpbmNvZGVOYW1lGhJNeUNoYWluY29kZVZlcnNpb24SigIK+QEKBDEyMzQS8AEtLS0tLUJFR0lOIFBSSVZBVEUgS0VZLS0tLS0KTUlHSEFnRUFNQk1HQnlxR1NNNDlBZ0VHQ0NxR1NNNDlBd0VIQkcwd2F3SUJBUVFnaG5BN3JkZ2JaaS93bmR1cwppWGp5ZjBLZ0U2T0taalErNUlOandlbFJBQzZoUkFOQ0FBU2IzdStoWStVL0ZadmhZRE42ZDA4SEoxdjU2VUpVCnl6L24yTkh5SmdUZzZrQzA1QWFKTWVHSWluRUYwSmVKdFJETlZRR3pvUUpRWWpuelVUUzlGdkdoCi0tLS0tRU5EIFBSSVZBVEUgS0VZLS0tLS0SDHUyM201azRoZjg2ahIGCgQKAhoA',
                'MEUCIQD9EDUxpvpBYtewKngRajsZy5YDcpDbPHD82hYdivM34QIgIvGTZW65l0vgsc87Ws3r3oq3140TYbZEChRLYk5pZE0=',
            ],
        ];
    }
}
