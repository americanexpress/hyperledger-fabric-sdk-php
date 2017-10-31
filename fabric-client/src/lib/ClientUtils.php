<?php
namespace AmericanExpress\FabricClient;

use Hyperledger\Fabric\Protos\Common;
use Google\Protobuf\Timestamp;
use Hyperledger\Fabric\Protos\Peer as Protos;

class ClientUtils
{

    /**
     * Function for getting current timestamp
     * @return Timestamp
     * This function will create a timestamp from the current time
     */
    public static function buildCurrentTimestamp()
    {
        $timestamp = new Timestamp();
        $microtime = microtime(true);
        $time = explode(".", $microtime);
        $seconds = $time[0];
        $nanos = (($microtime * 1000) % 1000) * 1000000;

        $timestamp->setSeconds($seconds);
        $timestamp->setNanos($nanos);

        return $timestamp;
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
     * @param $queryParam
     * @param $epoch
     * @param $TimeStamp
     * @return Common\ChannelHeader This function will build a common channel header
     * This function will build a common channel header
     * @internal param $channelID
     * @internal param $chainCodeName
     * @internal param $chainCodePath
     * @internal param $chainCodeVersion
     */
    public function createChannelHeader($type, $txID, $queryParam, $epoch, $TimeStamp)
    {
        $channelHeader = new Common\ChannelHeader();
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