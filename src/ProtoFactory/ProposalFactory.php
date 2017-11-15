<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\ProtoFactory;

use Hyperledger\Fabric\Protos\Common\Header;
use Hyperledger\Fabric\Protos\Peer\ChaincodeProposalPayload;
use Hyperledger\Fabric\Protos\Peer\Proposal;

class ProposalFactory
{
    /**
     * @param Header $header
     * @param ChaincodeProposalPayload $chaincodeProposalPayload
     * @return Proposal
     */
    public static function create(Header $header, ChaincodeProposalPayload $chaincodeProposalPayload): Proposal
    {
        $proposal = new Proposal();
        $proposal->setHeader($header->serializeToString());
        $proposal->setPayload($chaincodeProposalPayload->serializeToString());

        return $proposal;
    }
}
