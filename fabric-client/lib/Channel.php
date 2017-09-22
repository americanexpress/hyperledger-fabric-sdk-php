<?php

namespace fabric\sdk;

use fabric\sdk;

use Protos;
use Common;
use \Google\Protobuf\Internal;

class Channel
{
    static  $config = null;

    static $org = null;

    /**
     * @param $org
     * @param Protos\EndorserClient $connect
     * @param $channelId
     * @param $chainCodeName
     * @param $chainCodePath
     * @param $chainCodeVersion
     * @param $args
     * query chaincode installed on particular channel.
     */
    function queryByChainCode($org, Protos\EndorserClient $connect,$queyParam)
    {
        $utils = new \fabric\sdk\Utils();

        self::$config =  \Config::getOrgConfig($org);
        self::$org  = $org;
        $fabricProposal = $this->createFabricProposal($utils, $queyParam);

        return self::sendTransactionProposal($fabricProposal, \Config::loadDefaults("timeout"), $connect);

        // TODO
        // Set User Context
    }

    /**
     * @param Utils $utils
     * @param $channelId
     * @param $chainCodeName
     * @param $chainCodePath
     * @param $chainCodeVersion
     * @param $args
     * @return (Protos\Proposal) proposal using channelheader commonheader and chaincode invoke specification.
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

        $chainHeader = $clientUtils->createChannelHeader($ENDORSER_TRANSACTION, $txID, $queyParam, \Config::loadDefaults("epoch"), $TimeStamp);
        $chainHeaderString = $chainHeader->serializeToString();

        $chaincodeInvocationSpec = $utils->createChaincodeInvocationSpec($queyParam['args']);
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
     * @param $timeout
     * @param Protos\EndorserClient $connect
     * Builds client context.
     */
    function sendTransactionProposal(Protos\Proposal $request, $timeout, Protos\EndorserClient $connect)
    {
        return $this->sendTransaction($request, null, null, $connect);
    }

    /**
     * @param Protos\Proposal $request
     * @param $name
     * @param $clientContext
     * @param Protos\EndorserClient $connect
     * This method requests signed proposal and send transactional request to endorser.
     */
    static function sendTransaction(Protos\Proposal $request, $name, $clientContext, Protos\EndorserClient $connect)
    {
        $clientUtil = new ClientUtils();
        $request = $clientUtil->getSignedProposal($request, self::$org);

        list($proposalResponse, $status) = $connect->ProcessProposal($request)->wait();
        $status = ((array)$status);
        sleep(1);
        if (isset($status["code"]) && $status["code"] == 0) {
            return $proposalResponse->getPayload();
        }else{
            error_log("unable to get response");
        }

    }
}
