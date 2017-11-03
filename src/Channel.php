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

    private $config = [];
    private $org = '';

    /**
     * @param string $org
     * @param EndorserClient $connect
     * @param mixed[] $queryParams
     * @returns ProposalResponse
     */
    public function queryByChainCode(string $org, EndorserClient $connect, array $queryParams): ProposalResponse
    {
        $utils = new Utils();

        $this->config = AppConf::getOrgConfig($org);
        $this->org = $org;
        $fabricProposal = $this->createFabricProposal($utils, $queryParams);

        return self::sendTransactionProposal($fabricProposal, $connect);
    }

    /**
     * @param Utils $utils
     * @param mixed[] $queryParams
     * returns proposal using channelheader commonheader and chaincode invoke specification.
     * @return Proposal
     */
    private function createFabricProposal(Utils $utils, array $queryParams): Proposal
    {
        $clientUtils = new ClientUtils();
        $nonce = $utils->getNonce();
        $TransactionID = new TransactionID();
        $ccType = new ChaincodeSpec();
        $ccType->setType(self::DEFAULT_CHAINCODE_SPEC_TYPE);
        $txID = $TransactionID->getTxId($nonce, $this->org);
        $TimeStamp = $clientUtils->buildCurrentTimestamp();
        $chainHeader = $clientUtils->createChannelHeader(
            self::DEFAULT_CHANNEL_HEADER_TYPE,
            $txID,
            $queryParams,
            AppConf::loadDefaults("epoch"),
            $TimeStamp
        );
        $chainHeaderString = $chainHeader->serializeToString();
        $chaincodeInvocationSpec = $utils->createChaincodeInvocationSpec($queryParams["ARGS"]);
        $chaincodeInvocationSpecString = $chaincodeInvocationSpec->serializeToString();

        $payload = new ChaincodeProposalPayload();
        $payload->setInput($chaincodeInvocationSpecString);
        $payloadString = $payload->serializeToString();
        $identity = (new Identity())->createSerializedIdentity($this->config["admin_certs"], $this->config["mspid"]);

        $identityString = $identity->serializeToString();

        $headerString = $clientUtils->buildHeader($identityString, $chainHeaderString, $nonce);
        $proposal = new Proposal();
        $proposal->setHeader($headerString);
        $proposal->setPayload($payloadString);

        return $proposal;
    }

    /**
     * @param Proposal $request
     * @param EndorserClient $connect
     * Builds client context.
     * @return ProposalResponse
     */
    private function sendTransactionProposal(Proposal $request, EndorserClient $connect): ProposalResponse
    {
        return $this->sendTransaction($request, $connect);
    }

    /**
     * @param Proposal $request
     * @param EndorserClient $connect
     * This method requests signed proposal and send transactional request to endorser.
     * @return ProposalResponse
     */
    private function sendTransaction(Proposal $request, EndorserClient $connect): ProposalResponse
    {
        $clientUtil = new ClientUtils();
        $request = $clientUtil->getSignedProposal($request, $this->org);

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
