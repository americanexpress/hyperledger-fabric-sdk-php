<?php

namespace org\amex\fabric_client ;

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
        $nanos = (($microtime*1000) % 1000) * 1000000;

        $TimeStamp->setSeconds($seconds);
        $TimeStamp->setNanos($nanos);

        return $TimeStamp;
    }

    /**
    * Query using ChainCode
    * @param string $string
    * @return string
    */
    function getSignedProposal(Protos\Proposal $proposal)
    {
        $signedProposal = new Protos\SignedProposal();
        $proposalString = $proposal->serializeToString();
        $signedProposal->setProposalBytes($proposalString);

        $signatureString = (new \Hash())->signByteString($proposal);
        $signedProposal->setSignature($signatureString);

        return $signedProposal;
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
