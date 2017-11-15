<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\Factory\ChaincodeInvocationSpecFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Factory\SignedProposalFactory;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use function igorw\get_in;

class Channel
{
    /**
     * @var ClientConfigInterface
     */
    private $config;

    /**
     * @var Hash
     */
    private $hash;

    /**
     * @var TransactionID
     */
    private $transactionId;

    /**
     * @var EndorserClientManagerInterface
     */
    private $endorserClients;

    /**
     * Channel constructor.
     * @param ClientConfigInterface $config
     * @param EndorserClientManagerInterface $endorserClients
     * @param Hash $hash
     * @param TransactionID $transactionId
     */
    public function __construct(
        ClientConfigInterface $config,
        EndorserClientManagerInterface $endorserClients,
        Hash $hash,
        TransactionID $transactionId
    ) {
        $this->config = $config;
        $this->endorserClients = $endorserClients;
        $this->hash = $hash;
        $this->transactionId = $transactionId;
    }

    /**
     * @param ClientConfigInterface $config
     * @return Channel
     */
    public static function fromConfig(ClientConfigInterface $config): self
    {
        $hash = new Hash($config);
        $endorserClients = new EndorserClientManager($config);
        $transactionId = new TransactionID($config, $hash);

        return new self($config, $endorserClients, $hash, $transactionId);
    }

    /**
     * @param string $org
     * @param mixed[] $queryParams
     * @returns ProposalResponse
     */
    public function queryByChainCode(string $org, array $queryParams): ProposalResponse
    {
        $endorserClient = $this->endorserClients->get($org);

        $proposal = $this->createFabricProposal($org, $queryParams);

        return $this->sendTransaction($proposal, $endorserClient, $org);
    }

    /**
     * @param string $org
     * @param mixed[] $queryParams
     * @param string $network
     * returns proposal using channelheader commonheader and chaincode invoke specification.
     * @return Proposal
     */
    private function createFabricProposal(string $org, array $queryParams, string $network = 'test-network'): Proposal
    {
        $nonce = $this->hash->getNonce();
        $transactionId = $this->transactionId->getTxId($nonce, $org);
        $chainHeader = ChannelHeaderFactory::create(
            $transactionId,
            (string) get_in($queryParams, ['CHANNEL_ID']),
            (string) get_in($queryParams, ['CHAINCODE_PATH']),
            (string) get_in($queryParams, ['CHAINCODE_NAME']),
            (string) get_in($queryParams, ['CHAINCODE_VERSION']),
            $this->config->getIn(['epoch'])
        );
        $chaincodeInvocationSpec = ChaincodeInvocationSpecFactory::fromArgs(get_in($queryParams, ['ARGS']));
        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpec(
            $chaincodeInvocationSpec
        );
        $config = $this->config->getIn([$network, $org], null);
        $identity = SerializedIdentityFactory::fromFile(
            (string) get_in($config, ['mspid']),
            new \SplFileObject((string) get_in($config, ['admin_certs']))
        );

        $header = HeaderFactory::create($identity, $chainHeader, $nonce);

        return ProposalFactory::create($header, $chaincodeProposalPayload);
    }

    /**
     * @param Proposal $proposal
     * @param EndorserClient $endorserClient
     * @param string $org
     * This method requests signed proposal and send transactional request to endorser.
     * @return ProposalResponse
     */
    private function sendTransaction(Proposal $proposal, EndorserClient $endorserClient, string $org): ProposalResponse
    {
        $signature = $this->hash->signByteString($proposal, $org);

        $signedProposal = SignedProposalFactory::fromProposal($proposal, $signature);

        /** @var UnaryCall $simpleSurfaceActiveCall */
        $simpleSurfaceActiveCall = $endorserClient->ProcessProposal($signedProposal);
        list($proposalResponse, $status) = $simpleSurfaceActiveCall->wait();
        $status = (array)$status;
        if (get_in($status, ['code']) === 0) {
            return $proposalResponse;
        }

        \error_log('unable to get response');

        return null;
    }
}
