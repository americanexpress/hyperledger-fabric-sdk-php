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

    function __construct()
    {

    }

    function queryByChainCode($org, Protos\EndorserClient $connect, $channelId, $chainCodeName, $chainCodePath, $chainCodeVersion)
    {
        $utils = new \fabric\sdk\Utils();

        self::$config =  \Config::getOrgConfig($org);
        self::$org  = $org;

        $fabricProposal = $this->createFabricProposal($utils, $channelId, $chainCodeName, $chainCodePath, $chainCodeVersion);

        self::sendTransactionProposal($fabricProposal, \Config::loadDefaults("timeout"), $connect);

        // TODO
        // Set User Context
    }

    public function createFabricProposal(Utils $utils, $channelId, $chainCodeName, $chainCodePath, $chainCodeVersion)
    {

        $clientUtils = new ClientUtils();

        $nounce = $utils::getNonce();

        $TransactionID = new TransactionID();

        $ccType = new Protos\ChaincodeSpec();

        $ccType->setType(Constants::$GoLang);

        $chaincodeHeaderExtension = new Protos\ChaincodeHeaderExtension();
        $chaincodeHeaderExtension->setChaincodeId($chaincodeID);

        $ENDORSER_TRANSACTION = Constants::$Endorsor;
        $txID = $TransactionID->getTxId($nounce, self::$org);
        $TimeStamp = $clientUtils->buildCurrentTimestamp();

        $chainHeader = $clientUtils->createChannelHeader($ENDORSER_TRANSACTION, $txID, $channelId, \Config::loadDefaults("epoch"), $TimeStamp, $chainCodeName, $chainCodePath, $chainCodeVersion);
        $chainHeaderString = $chainHeader->serializeToString();

        $chaincodeInvocationSpec = $utils->createChaincodeInvocationSpec($chaincodeID, $ccType);
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


    function sendTransactionProposal(Protos\Proposal $request, $timeout, Protos\EndorserClient $connect)
    {
        return $this->sendTransaction($request, null, null, $connect);
    }

    static function sendTransaction(Protos\Proposal $request, $name, $clientContext, Protos\EndorserClient $connect)
    {
        $clientUtil = new ClientUtils();
        $request = $clientUtil->getSignedProposal($request, self::$org);

        list($proposalResponse, $status) = $connect->ProcessProposal($request)->wait();
        $status = ((array)$status);
        if (isset($status["code"]) && $status["code"] == 0) {
            print_r($proposalResponse->getPayload());
        } else {
            echo 'status is not 0';
            sleep(5);
        }
    }


    function getTransactionId($protoUtils, $nounce)
    {
        $common = new Common();
        return $common->getTxId($protoUtils, $nounce);
    }
}
