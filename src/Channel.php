<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\MSP\Identity;
use Grpc\ChannelCredentials;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer\ChaincodeProposalPayload;
use Hyperledger\Fabric\Protos\Peer\ChaincodeSpec;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;

class Channel
{
    private const DEFAULT_CHAINCODE_SPEC_TYPE = 1;
    private const DEFAULT_CHANNEL_HEADER_TYPE = 3;

    /**
     * @var ClientConfigInterface
     */
    private $config;

    /**
     * @var Utils
     */
    private $utils;

    /**
     * @var Identity
     */
    private $identity;

    /**
     * @var ClientUtils
     */
    private $clientUtils;

    /**
     * @var TransactionID
     */
    private $transactionId;

    /**
     * @var EndorserClient[]
     */
    private $endorserClients = [];

    /**
     * Channel constructor.
     * @param ClientConfigInterface $config
     * @param Utils $utils
     * @param Identity $identity
     * @param ClientUtils $clientUtils
     * @param TransactionID $transactionId
     */
    public function __construct(
        ClientConfigInterface $config,
        Utils $utils,
        Identity $identity,
        ClientUtils $clientUtils,
        TransactionID $transactionId
    ) {
        $this->config = $config;
        $this->utils = $utils;
        $this->identity = $identity;
        $this->clientUtils = $clientUtils;
        $this->transactionId = $transactionId;
    }

    /**
     * @param ClientConfigInterface $config
     * @return Channel
     */
    public static function fromConfig(ClientConfigInterface $config): self
    {
        $utils = new Utils($config);
        $hash = new Hash($config, $utils);
        $clientUtils = new ClientUtils($config, $hash);
        $identity = new Identity();
        $transactionId = new TransactionID($config, $identity, $utils);

        return new self($config, $utils, $identity, $clientUtils, $transactionId);
    }

    /**
     * @param string $org
     * @param mixed[] $queryParams
     * @returns ProposalResponse
     */
    public function queryByChainCode(string $org, array $queryParams): ProposalResponse
    {
        $endorserClient = $this->getEndorserClient($org);

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
        $nonce = $this->utils->getNonce();
        $ccType = new ChaincodeSpec();
        $ccType->setType(self::DEFAULT_CHAINCODE_SPEC_TYPE);
        $txID = $this->transactionId->getTxId($nonce, $org);
        $TimeStamp = $this->clientUtils->buildCurrentTimestamp();
        $chainHeader = $this->clientUtils->createChannelHeader(
            self::DEFAULT_CHANNEL_HEADER_TYPE,
            $txID,
            $queryParams,
            ClientConfig::getInstance()->getIn(['epoch']),
            $TimeStamp
        );
        $chainHeaderString = $chainHeader->serializeToString();
        $chaincodeInvocationSpec = ChaincodeInvocationSpecFactory::fromArgs($queryParams['ARGS']);
        $chaincodeInvocationSpecString = $chaincodeInvocationSpec->serializeToString();

        $payload = new ChaincodeProposalPayload();
        $payload->setInput($chaincodeInvocationSpecString);
        $payloadString = $payload->serializeToString();
        $config = $this->config->getIn([$network, $org], null);
        $identity = $this->identity->createSerializedIdentity($config['admin_certs'], $config['mspid']);

        $identityString = $identity->serializeToString();

        $headerString = $this->clientUtils->buildHeader($identityString, $chainHeaderString, $nonce);
        $proposal = new Proposal();
        $proposal->setHeader($headerString);
        $proposal->setPayload($payloadString);

        return $proposal;
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
        $signedProposal = $this->clientUtils->getSignedProposal($proposal, $org);

        /** @var UnaryCall $simpleSurfaceActiveCall */
        $simpleSurfaceActiveCall = $endorserClient->ProcessProposal($signedProposal);
        list($proposalResponse, $status) = $simpleSurfaceActiveCall->wait();
        $status = (array)$status;
        if (isset($status["code"]) && $status["code"] == 0) {
            return $proposalResponse;
        } else {
            error_log("unable to get response");
        }
        return null;
    }

    /**
     * Read connection configuration.
     * @param string $org
     * @param string $network
     * @param string $peer
     * @return EndorserClient
     */
    private function getEndorserClient(
        string $org,
        string $network = 'test-network',
        string $peer = 'peer1'
    ): EndorserClient {
        $host = $this->config->getIn([$network, $org, $peer, 'requests'], null);

        if (!\array_key_exists($host, $this->endorserClients)) {
            $this->endorserClients[$host] = new EndorserClient($host, [
                'credentials' => ChannelCredentials::createInsecure(),
            ]);
        }

        return $this->endorserClients[$host];
    }
}
