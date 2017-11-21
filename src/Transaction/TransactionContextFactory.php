<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Transaction;

use AmericanExpress\HyperledgerFabricClient\ChannelContext;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Nonce\NonceGeneratorInterface;

final class TransactionContextFactory implements TransactionContextFactoryInterface
{
    /**
     * @var NonceGeneratorInterface
     */
    private $nonceGenerator;

    /**
     * @var TxIdFactoryInterface
     */
    private $txIdFactory;

    /**
     * @var int
     */
    private $epoch = 0;

    /**
     * @param NonceGeneratorInterface $nonceGenerator
     * @param TxIdFactoryInterface $txIdFactory
     * @param int $epoch
     */
    public function __construct(
        NonceGeneratorInterface $nonceGenerator,
        TxIdFactoryInterface $txIdFactory,
        int $epoch = 0
    ) {
        $this->nonceGenerator = $nonceGenerator;
        $this->txIdFactory = $txIdFactory;
        $this->epoch = $epoch;
    }

    /**
     * @param ChannelContext $channelContext
     * @return TransactionContext
     */
    public function fromChannelContext(ChannelContext $channelContext): TransactionContext
    {
        $identity = SerializedIdentityFactory::fromFile($channelContext->getMspId(), $channelContext->getAdminCerts());

        $nonce = $this->nonceGenerator->generateNonce();

        $txId = $this->txIdFactory->fromSerializedIdentity($identity, $nonce);

        return new TransactionContext($identity, $nonce, $txId, $this->epoch);
    }
}
