<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\Cryptography\CryptographyInterface;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeInvocationSpecFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignedProposalFactory;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use function igorw\get_in;

class Channel implements ChannelInterface
{
    /**
     * @var CryptographyInterface
     */
    private $cryptography;

    /**
     * @var EndorserClientManagerInterface
     */
    private $endorserClients;

    /**
     * Channel constructor.
     * @param EndorserClientManagerInterface $endorserClients
     * @param CryptographyInterface $cryptography
     */
    public function __construct(
        EndorserClientManagerInterface $endorserClients,
        CryptographyInterface $cryptography
    ) {
        $this->endorserClients = $endorserClients;
        $this->cryptography = $cryptography;
    }

    /**
     * @param ChannelContext $context
     * @param ChaincodeQueryParams $params
     * @return ProposalResponse
     */
    public function queryByChainCode(
        ChannelContext $context,
        ChaincodeQueryParams $params
    ): ProposalResponse {
        $host = $context->getHost();

        $endorserClient = $this->endorserClients->get($host);

        $proposal = $this->createFabricProposal($context, $params);

        return $this->sendTransaction($context, $proposal, $endorserClient);
    }

    /**
     * @param ChannelContext $context
     * @param ChaincodeQueryParams $params
     * returns proposal using channelheader commonheader and chaincode invoke specification.
     * @return Proposal
     */
    private function createFabricProposal(ChannelContext $context, ChaincodeQueryParams $params): Proposal
    {
        $identity = SerializedIdentityFactory::fromFile($context->getMspId(), $context->getAdminCerts());

        $nonce = $this->cryptography->getNonce();

        $transactionId = $this->cryptography->createTxId($identity, $nonce);

        $chainHeader = ChannelHeaderFactory::create(
            $transactionId,
            $params->getChannelId(),
            $params->getChaincodePath(),
            $params->getChaincodeName(),
            $params->getChaincodeVersion(),
            $context->getEpoch()
        );

        $chaincodeInvocationSpec = ChaincodeInvocationSpecFactory::fromArgs($params->getArgs());

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpec(
            $chaincodeInvocationSpec
        );

        $header = HeaderFactory::create($identity, $chainHeader, $nonce);

        return ProposalFactory::create($header, $chaincodeProposalPayload);
    }

    /**
     * @param ChannelContext $context
     * @param Proposal $proposal
     * @param EndorserClient $endorserClient
     * @return ProposalResponse
     */
    private function sendTransaction(
        ChannelContext $context,
        Proposal $proposal,
        EndorserClient $endorserClient
    ): ProposalResponse {
        $privateKey = $context->getPrivateKey();

        $signature = $this->cryptography->signByteString($proposal, $privateKey);

        $signedProposal = SignedProposalFactory::fromProposal($proposal, $signature);

        /** @var UnaryCall $simpleSurfaceActiveCall */
        $simpleSurfaceActiveCall = $endorserClient->ProcessProposal($signedProposal);
        list($proposalResponse, $status) = $simpleSurfaceActiveCall->wait();
        $status = (array)$status;
        if (get_in($status, ['code']) === 0) {
            return $proposalResponse;
        }

        \error_log('unable to get response');

        return null;
    }
}
