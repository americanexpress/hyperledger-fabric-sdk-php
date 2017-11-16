<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Signatory;

use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;

interface SignatoryInterface
{
    /**
     * @param Proposal $proposal
     * @param \SplFileObject $privateKey
     * @return SignedProposal
     */
    public function signProposal(Proposal $proposal, \SplFileObject $privateKey): SignedProposal;
}
