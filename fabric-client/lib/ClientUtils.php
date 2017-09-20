<?php

namespace fabric\sdk;

use Protos;
use Common;

class ClientUtils
{

    /**
     * Function for getting current timestamp
     * @return Object containing Seconds & Nanoseconds
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

    function getSignedProposal(Protos\Proposal $proposal)
    {
        $signedProposal = new Protos\SignedProposal();
        $proposalString = $proposal->serializeToString();
        $signedProposal->setProposalBytes($proposalString);

        $signatureString = (new \Hash())->signByteString($proposal);
        $signedProposal->setSignature($signatureString);

        return $signedProposal;
    }

    public function createChannelHeader($type, $txID, $channelID, $epoch, $TimeStamp, $chainCodeName, $chainCodePath, $chainCodeVersion)
    {
        $channelHeader = new \Common\ChannelHeader();
        $channelHeader->setType($type);
        $channelHeader->setVersion(1);
        $channelHeader->setTxId($txID);
        $channelHeader->setChannelId($channelID);
        $channelHeader->setEpoch($epoch);
        $channelHeader->setTimestamp($TimeStamp);

        $chainCodeId = new Protos\ChaincodeID();
        $chainCodeId->setPath($chainCodePath);
        $chainCodeId->setName($chainCodeName);
        $chainCodeId->setVersion($chainCodeVersion);
        $chaincodeHeaderExtension = new Protos\ChaincodeHeaderExtension();
        $chaincodeHeaderExtension->setChaincodeId($chainCodeId);
        $chaincodeHeaderExtensionString = $chaincodeHeaderExtension->serializeToString();
        $channelHeader->setExtension($chaincodeHeaderExtensionString);

        return $channelHeader;
    }

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
