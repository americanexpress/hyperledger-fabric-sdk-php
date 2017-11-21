<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Nonce;

use AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException;
use Assert\Assertion;
use Assert\AssertionFailedException;

final class RandomBytesNonceGenerator implements NonceGeneratorInterface
{
    /**
     * @var int
     */
    private $nonceSize;

    /**
     * Utils constructor.
     * @param int $nonceSize
     */
    public function __construct(int $nonceSize = 24)
    {
        try {
            Assertion::greaterThan($nonceSize, -1);
        } catch (AssertionFailedException $e) {
            throw InvalidArgumentException::fromException($e);
        }

        $this->nonceSize = $nonceSize;
    }
    /**
     * @return string
     */
    public function generateNonce(): string
    {
        return \random_bytes($this->nonceSize);
    }
}
