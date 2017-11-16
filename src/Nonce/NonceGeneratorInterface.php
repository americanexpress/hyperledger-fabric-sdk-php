<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Nonce;

interface NonceGeneratorInterface
{
    /**
     * @return string
     */
    public function generateNonce(): string;
}
