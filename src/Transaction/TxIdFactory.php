<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Transaction;

use AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException;
use AmericanExpress\HyperledgerFabricClient\Serializer\BinaryStringSerializer;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

class TxIdFactory implements TxIdFactoryInterface
{
    /**
     * @var string
     */
    private $hashAlgorithm;

    /**
     * @var BinaryStringSerializer
     */
    private $binaryStringSerializer;

    /**
     * @param string $hashAlgorithm
     */
    public function __construct(string $hashAlgorithm = 'sha256')
    {
        try {
            Assertion::inArray($hashAlgorithm, hash_algos());
        } catch (AssertionFailedException $e) {
            throw InvalidArgumentException::fromException($e);
        }

        $this->hashAlgorithm = $hashAlgorithm;
        $this->binaryStringSerializer = new BinaryStringSerializer();
    }

    /**
     * @param SerializedIdentity $serializedIdentity
     * @param string $nonce
     * @return string
     */
    public function fromSerializedIdentity(SerializedIdentity $serializedIdentity, string $nonce): string
    {
        $noArray = $this->binaryStringSerializer->deserialize($nonce);

        $identityArray = $this->binaryStringSerializer->deserialize($serializedIdentity->serializeToString());

        $comp = \array_merge($noArray, $identityArray);

        $compString = $this->binaryStringSerializer->serialize($comp);

        return \hash($this->hashAlgorithm, $compString);
    }
}
