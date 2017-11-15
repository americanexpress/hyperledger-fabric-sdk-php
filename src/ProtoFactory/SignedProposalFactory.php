<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\ProtoFactory;

use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;

class SignedProposalFactory
{
    /**
     * @param Proposal $proposal
     * @param string $signature
     * @return SignedProposal
     * This function will sign proposal
     */
    public static function fromProposal(Proposal $proposal, string $signature): SignedProposal
    {
        $signedProposal = new SignedProposal();
        $signedProposal->setProposalBytes($proposal->serializeToString());
        $signedProposal->setSignature($signature);

        return $signedProposal;
    }
}
