<?php

namespace AmericanExpress\FabricClient;

use Protos;
use Common;

class ClientUtils
{

    /**
     * Function for getting current timestamp
     * @return Object containing Seconds & Nanoseconds
     * This function will create a timestamp from the current time
     */
    public static function buildCurrentTimestamp()
    {
        $TimeStamp = new \Google\Protobuf\Timestamp();
        $microtime = microtime(true);
        $time = explode(".", $microtime);
        $seconds = $time[0];
        $nanos = (($microtime * 1000) % 1000) * 1000000;

        $TimeStamp->setSeconds($seconds);
        $TimeStamp->setNanos($nanos);

        return $TimeStamp;
    }

    /**
     * @param Protos\Proposal $proposal
     * @param $org
     * @return Protos\SignedProposal
     * This function will sign proposal
     */
    function getSignedProposal(Protos\Proposal $proposal, $org)
    {
        $signedProposal = new Protos\SignedProposal();
        $proposalString = $proposal->serializeToString();
        $signedProposal->setProposalBytes($proposalString);

        $signatureString = (new Hash())->signByteString($proposal, $org);
        $signedProposal->setSignature($signatureString);

        return $signedProposal;
    }

    /**
     * @param $type
     * @param $txID
     * @param $channelID
     * @param $epoch
     * @param $TimeStamp
     * @param $chainCodeName
     * @param $chainCodePath
     * @param $chainCodeVersion
     * @return Common\ChannelHeader
     *This function will build a common channel header
     */
    public function createChannelHeader($type, $txID, $queryParam, $epoch, $TimeStamp)
    {
        $channelHeader = new \Common\ChannelHeader();
        $channelHeader->setType($type);
        $channelHeader->setVersion(1);
        $channelHeader->setTxId($txID);
        $channelHeader->setChannelId($queryParam["CHANNEL_ID"]);
        $channelHeader->setEpoch($epoch);
        $channelHeader->setTimestamp($TimeStamp);

        $chainCodeId = new Protos\ChaincodeID();
        $chainCodeId->setPath($queryParam["CHAINCODE_PATH"]);
        $chainCodeId->setName($queryParam["CHAINCODE_NAME"]);
        $chainCodeId->setVersion($queryParam["CHAINCODE_VERSION"]);
        $chaincodeHeaderExtension = new Protos\ChaincodeHeaderExtension();
        $chaincodeHeaderExtension->setChaincodeId($chainCodeId);
        $chaincodeHeaderExtensionString = $chaincodeHeaderExtension->serializeToString();
        $channelHeader->setExtension($chaincodeHeaderExtensionString);

        return $channelHeader;
    }

    /**
     * @param $creator
     * @param $channelHeader
     * @param $nounce
     * @return string
     *  This function will build the common header
     */
    function buildHeader($creator, $channelHeader, $nounce)
    {
        $signatureHeader = new Common\SignatureHeader();
        $signatureHeader->setCreator($creator);
        $signatureHeader->setNonce($nounce);

        $signatureHeaderString = $signatureHeader->serializeToString();
        $header = new Common\Header();
        $header->setSignatureHeader($signatureHeaderString);
        $header->setChannelHeader($channelHeader);
        $headerString = $header->serializeToString();
        return $headerString;
    }
}