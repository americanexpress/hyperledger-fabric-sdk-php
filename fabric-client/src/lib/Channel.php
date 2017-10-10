<?php

namespace AmericanExpress\FabricClient;

use AmericanExpress\FabricClient\msp\Identity;
use Protos;

class Channel
{
    static $config = null;
    static $org = null;

    /**
     * @param $org
     * @param Protos\EndorserClient $connect
     * @param $connect
     * @param $queyParam
     * @returns Protos\Proposal
     */
    function queryByChainCode($org, Protos\EndorserClient $connect, $queyParam)
    {
        $utils = new Utils();

        self::$config = AppConf::getOrgConfig($org);
        self::$org = $org;
        $fabricProposal = $this->createFabricProposal($utils, $queyParam);

        return self::sendTransactionProposal($fabricProposal, $connect);
    }

    /**
     * @param Utils $utils
     * @param $queyParam
     * returns proposal using channelheader commonheader and chaincode invoke specification.
     * @return Protos\Proposal
     */
    public function createFabricProposal(Utils $utils, $queyParam)
    {

        $clientUtils = new ClientUtils();
        $nounce = $utils::getNonce();
        $TransactionID = new TransactionID();
        $ccType = new Protos\ChaincodeSpec();
        $ccType->setType(Constants::$GoLang);
        $ENDORSER_TRANSACTION = Constants::$Endorsor;
        $txID = $TransactionID->getTxId($nounce, self::$org);
        $TimeStamp = $clientUtils->buildCurrentTimestamp();
        $chainHeader = $clientUtils->createChannelHeader($ENDORSER_TRANSACTION, $txID, $queyParam,
            AppConf::loadDefaults("epoch"), $TimeStamp);
        $chainHeaderString = $chainHeader->serializeToString();
        $chaincodeInvocationSpec = $utils->createChaincodeInvocationSpec($queyParam["ARGS"]);
        $chaincodeInvocationSpecString = $chaincodeInvocationSpec->serializeToString();

        $payload = new Protos\ChaincodeProposalPayload();
        $payload->setInput($chaincodeInvocationSpecString);
        $payloadString = $payload->serializeToString();
        $identity = (new Identity())->createSerializedIdentity(self::$config["admin_certs"], self::$config["mspid"]);

        $identitystring = $identity->serializeToString();

        $headerString = $clientUtils->buildHeader($identitystring, $chainHeaderString, $nounce);
        $proposal = new Protos\Proposal();
        $proposal->setHeader($headerString);
        $proposal->setPayload($payloadString);

        return $proposal;
    }

    /**
     * @param Protos\Proposal $request
     * @param Protos\EndorserClient $connect
     * Builds client context.
     * @return null
     */
    function sendTransactionProposal(Protos\Proposal $request, Protos\EndorserClient $connect)
    {
        return $this->sendTransaction($request, $connect);
    }

    /**
     * @param Protos\Proposal $request
     * @param Protos\EndorserClient $connect
     * This method requests signed proposal and send transactional request to endorser.
     * @return null
     */
    static function sendTransaction(Protos\Proposal $request, Protos\EndorserClient $connect)
    {
        $clientUtil = new ClientUtils();
        $request = $clientUtil->getSignedProposal($request, self::$org);

        list($proposalResponse, $status) = $connect->ProcessProposal($request)->wait();
        $status = ((array)$status);
        sleep(1);
        if (isset($status["code"]) && $status["code"] == 0) {
            return $proposalResponse->getPayload();
        } else {
            error_log("unable to get response");
        }

        return null;
    }
}
