<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

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
    public function buildCurrentTimestamp(): Timestamp
    {
        $timestamp = new Timestamp();
        $microtime = microtime(true);
        $time = explode(".", (string) $microtime);
        $seconds = $time[0];
        $nanos = (($microtime * 1000) % 1000) * 1000000;

        $timestamp->setSeconds($seconds);
        $timestamp->setNanos($nanos);

        return $timestamp;
    }

    /**
     * @param Protos\Proposal $proposal
     * @param string $org
     * @return Protos\SignedProposal
     * This function will sign proposal
     */
    public function getSignedProposal(Protos\Proposal $proposal, string $org): Protos\SignedProposal
    {
        $signedProposal = new Protos\SignedProposal();
        $proposalString = $proposal->serializeToString();
        $signedProposal->setProposalBytes($proposalString);

        $signatureString = (new Hash())->signByteString($proposal, $org);
        $signedProposal->setSignature($signatureString);

        return $signedProposal;
    }

    /**
     * @param int $type
     * @param string $txID
     * @param mixed[] $queryParam
     * @param int|string $epoch
     * @param Timestamp $timestamp
     * @return Common\ChannelHeader This function will build a common channel header
     * This function will build a common channel header
     */
    public function createChannelHeader(int $type, string $txID, array $queryParam, $epoch, Timestamp $timestamp): Common\ChannelHeader
    {
        $channelHeader = new Common\ChannelHeader();
        $channelHeader->setType($type);
        $channelHeader->setVersion(1);
        $channelHeader->setTxId($txID);
        $channelHeader->setChannelId($queryParam["CHANNEL_ID"]);
        $channelHeader->setEpoch($epoch);
        $channelHeader->setTimestamp($timestamp);

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
     * @param string $creator
     * @param string $channelHeader
     * @param string $nonce
     * @return string
     *  This function will build the common header
     */
    public function buildHeader(string $creator, string $channelHeader, string $nonce): string
    {
        $signatureHeader = new Common\SignatureHeader();
        $signatureHeader->setCreator($creator);
        $signatureHeader->setNonce($nonce);

        $signatureHeaderString = $signatureHeader->serializeToString();
        $header = new Common\Header();
        $header->setSignatureHeader($signatureHeaderString);
        $header->setChannelHeader($channelHeader);
        $headerString = $header->serializeToString();
        return $headerString;
    }
}
