<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use Google\Protobuf\Timestamp;
use Hyperledger\Fabric\Protos\Common\ChannelHeader;
use Hyperledger\Fabric\Protos\Common\Header;
use Hyperledger\Fabric\Protos\Common\SignatureHeader;
use Hyperledger\Fabric\Protos\Peer\ChaincodeHeaderExtension;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;

class ClientUtils
{
    /**
     * @var ClientConfigInterface
     */
    private $config;

    /**
     * @var Hash
     */
    private $hash;

    /**
     * Utils constructor.
     * @param ClientConfigInterface $config
     */
    public function __construct(ClientConfigInterface $config)
    {
        $this->config = $config;
        $this->hash = new Hash($config);
    }

    /**
     * Function for getting current timestamp
     * @return Timestamp
     * This function will create a timestamp from the current time
     */
    public function buildCurrentTimestamp(): Timestamp
    {
        return TimestampFactory::fromDateTime();
    }

    /**
     * @param Proposal $proposal
     * @param string $org
     * @return SignedProposal
     * This function will sign proposal
     */
    public function getSignedProposal(Proposal $proposal, string $org): SignedProposal
    {
        $signedProposal = new SignedProposal();
        $proposalString = $proposal->serializeToString();
        $signedProposal->setProposalBytes($proposalString);

        $signatureString = $this->hash->signByteString($proposal, $org);
        $signedProposal->setSignature($signatureString);

        return $signedProposal;
    }

    /**
     * @param int $type
     * @param string $txID
     * @param mixed[] $queryParam
     * @param int|string $epoch
     * @param Timestamp $timestamp
     * @return ChannelHeader This function will build a common channel header
     * This function will build a common channel header
     */
    public function createChannelHeader(
        int $type,
        string $txID,
        array $queryParam,
        $epoch,
        Timestamp $timestamp
    ): ChannelHeader {
        $channelHeader = new ChannelHeader();
        $channelHeader->setType($type);
        $channelHeader->setVersion(1);
        $channelHeader->setTxId($txID);
        $channelHeader->setChannelId($queryParam["CHANNEL_ID"]);
        $channelHeader->setEpoch($epoch);
        $channelHeader->setTimestamp($timestamp);

        $chainCodeId = new ChaincodeID();
        $chainCodeId->setPath($queryParam["CHAINCODE_PATH"]);
        $chainCodeId->setName($queryParam["CHAINCODE_NAME"]);
        $chainCodeId->setVersion($queryParam["CHAINCODE_VERSION"]);
        $chaincodeHeaderExtension = new ChaincodeHeaderExtension();
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
        $signatureHeader = new SignatureHeader();
        $signatureHeader->setCreator($creator);
        $signatureHeader->setNonce($nonce);

        $signatureHeaderString = $signatureHeader->serializeToString();
        $header = new Header();
        $header->setSignatureHeader($signatureHeaderString);
        $header->setChannelHeader($channelHeader);
        $headerString = $header->serializeToString();
        return $headerString;
    }
}
