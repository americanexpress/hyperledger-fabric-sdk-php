<?php
declare(strict_types=1);

namespace AmericanExpress\FabricClient;

use AmericanExpress\FabricClient\msp\Identity;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer as Protos;

class Channel
{
    private const DEFAULT_CHAINCODE_SPEC_TYPE = 1;
    private const DEFAULT_CHANNEL_HEADER_TYPE = 3;

    private $config = [];
    private $org = '';

    /**
     * @param string $org
     * @param Protos\EndorserClient $connect
     * @param mixed[] $queryParams
     * @returns Protos\ProposalResponse
     */
    public function queryByChainCode(string $org, Protos\EndorserClient $connect, array $queryParams): Protos\ProposalResponse
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
     * @return Protos\Proposal
     */
    private function createFabricProposal(Utils $utils, array $queryParams): Protos\Proposal
    {
        $clientUtils = new ClientUtils();
        $nonce = $utils->getNonce();
        $TransactionID = new TransactionID();
        $ccType = new Protos\ChaincodeSpec();
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

        $payload = new Protos\ChaincodeProposalPayload();
        $payload->setInput($chaincodeInvocationSpecString);
        $payloadString = $payload->serializeToString();
        $identity = (new Identity())->createSerializedIdentity($this->config["admin_certs"], $this->config["mspid"]);

        $identityString = $identity->serializeToString();

        $headerString = $clientUtils->buildHeader($identityString, $chainHeaderString, $nonce);
        $proposal = new Protos\Proposal();
        $proposal->setHeader($headerString);
        $proposal->setPayload($payloadString);

        return $proposal;
    }

    /**
     * @param Protos\Proposal $request
     * @param Protos\EndorserClient $connect
     * Builds client context.
     * @return Protos\ProposalResponse
     */
    private function sendTransactionProposal(Protos\Proposal $request, Protos\EndorserClient $connect): Protos\ProposalResponse
    {
        return $this->sendTransaction($request, $connect);
    }

    /**
     * @param Protos\Proposal $request
     * @param Protos\EndorserClient $connect
     * This method requests signed proposal and send transactional request to endorser.
     * @return Protos\ProposalResponse
     */
    private function sendTransaction(Protos\Proposal $request, Protos\EndorserClient $connect): Protos\ProposalResponse
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
