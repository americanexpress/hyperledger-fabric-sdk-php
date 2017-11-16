<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use Hyperledger\Fabric\Protos\Peer\ProposalResponse;

interface ChannelInterface
{
    /**
     * @param ChannelContext $context
     * @param ChaincodeQueryParams $params
     * @return ProposalResponse
     */
    public function queryByChainCode(ChannelContext $context, ChaincodeQueryParams $params): ProposalResponse;
}
