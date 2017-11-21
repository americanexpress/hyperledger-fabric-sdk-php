<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Transaction;

use AmericanExpress\HyperledgerFabricClient\Serializer\BinaryStringSerializer;
use AmericanExpress\HyperledgerFabricClient\ValueObject\HashAlgorithm;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

final class TxIdFactory implements TxIdFactoryInterface
{
    /**
     * @var HashAlgorithm
     */
    private $hashAlgorithm;

    /**
     * @var BinaryStringSerializer
     */
    private $binaryStringSerializer;

    /**
     * @param HashAlgorithm $hashAlgorithm
     */
    public function __construct(HashAlgorithm $hashAlgorithm = null)
    {
        $this->hashAlgorithm = $hashAlgorithm ?: new HashAlgorithm();
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

        return \hash((string) $this->hashAlgorithm, $compString);
    }
}
