<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Transaction;

use AmericanExpress\HyperledgerFabricClient\ChannelContext;

interface TransactionContextFactoryInterface
{
    /**
     * @param ChannelContext $channelContext
     * @return TransactionContext
     */
    public function fromChannelContext(ChannelContext $channelContext): TransactionContext;
}
