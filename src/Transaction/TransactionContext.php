<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Transaction;

use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

class TransactionContext
{
    /**
     * @var SerializedIdentity
     */
    private $serializedIdentity;

    /**
     * @var string
     */
    private $nonce;

    /**
     * @var string
     */
    private $txId;

    /**
     * @var int
     */
    private $epoch = 0;

    /**
     * @param SerializedIdentity $serializedIdentity
     * @param string $nonce
     * @param string $txId
     * @param int $epoch
     */
    public function __construct(SerializedIdentity $serializedIdentity, string $nonce, string $txId, int $epoch = 0)
    {
        $this->serializedIdentity = $serializedIdentity;
        $this->nonce = $nonce;
        $this->txId = $txId;
        $this->epoch = $epoch;
    }

    /**
     * @return SerializedIdentity
     */
    public function getSerializedIdentity(): SerializedIdentity
    {
        return $this->serializedIdentity;
    }

    /**
     * @return string
     */
    public function getNonce(): string
    {
        return $this->nonce;
    }

    /**
     * @return string
     */
    public function getTxId(): string
    {
        return $this->txId;
    }

    /**
     * @return int
     */
    public function getEpoch(): int
    {
        return $this->epoch;
    }
}
