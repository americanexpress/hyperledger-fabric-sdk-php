<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\MSP\Identity;

class TransactionID
{
    /**
     * @param string $nonce
     * @param string $org
     * @return string
     * Generate transaction Id using Ecert of member.
     */
    public function getTxId(string $nonce, string $org): string
    {
        $config = ClientConfig::getOrgConfig($org);

        $identity = (new Identity)->createSerializedIdentity($config["admin_certs"], $config["mspid"]);
        $identityString = $identity->serializeToString();

        $utils = new Utils();
        $noArray = $utils->toByteArray($nonce);
        $identityArray = $utils->toByteArray($identityString);
        $comp = array_merge($noArray, $identityArray);
        $compString = $utils->proposalArrayToBinaryString($comp);
        $txID = hash('sha256', $compString);

        return $txID;
    }
}
