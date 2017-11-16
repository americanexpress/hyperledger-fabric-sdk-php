<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\ValueObject;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;
use AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException;
use Assert\Assertion;
use Assert\AssertionFailedException;

class HashAlgorithm extends StringValue
{
    /**
     * HashAlgorithm constructor.
     * @param string $value
     */
    public function __construct(string $value = 'sha256')
    {
        try {
            Assertion::inArray($value, hash_algos());
        } catch (AssertionFailedException $e) {
            throw InvalidArgumentException::fromException($e);
        }

        parent::__construct($value);
    }

    /**
     * @param ClientConfigInterface $config
     * @return self
     */
    public static function fromConfig(ClientConfigInterface $config): self
    {
        return new self($config->getIn(['crypto-hash-algo']));
    }
}
