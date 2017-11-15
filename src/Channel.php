<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\MSP\Identity;
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
     * Channel constructor.
     * @param ClientConfigInterface $config
     */
    public function __construct(ClientConfigInterface $config)
    {
        $this->config = $config;
        $this->utils = new Utils($config);
        $this->identity = new Identity();
        $this->clientUtils = new ClientUtils($config);
        $this->transactionId = new TransactionID($config);
    }

    /**
     * @param string $org
     * @param mixed[] $queryParams
     * @returns ProposalResponse
     */
    public function queryByChainCode(string $org, array $queryParams): ProposalResponse
    {
        $connect = $this->utils->fabricConnect($org);

        $fabricProposal = $this->createFabricProposal($org, $queryParams);

        return $this->sendTransaction($fabricProposal, $connect, $org);
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
        $chaincodeInvocationSpec = $this->utils->createChaincodeInvocationSpec($queryParams["ARGS"]);
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
     * @param Proposal $request
     * @param EndorserClient $connect
     * @param string $org
     * This method requests signed proposal and send transactional request to endorser.
     * @return ProposalResponse
     */
    private function sendTransaction(Proposal $request, EndorserClient $connect, string $org): ProposalResponse
    {
        $request = $this->clientUtils->getSignedProposal($request, $org);

        /** @var UnaryCall $simpleSurfaceActiveCall */
        $simpleSurfaceActiveCall = $connect->ProcessProposal($request);
        list($proposalResponse, $status) = $simpleSurfaceActiveCall->wait();
        $status = (array)$status;
        if (isset($status["code"]) && $status["code"] == 0) {
            return $proposalResponse;
        } else {
            error_log("unable to get response");
        }
        return null;
    }
}
