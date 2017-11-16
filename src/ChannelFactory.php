<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigFactory;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;
use AmericanExpress\HyperledgerFabricClient\Nonce\RandomBytesNonceGenerator;
use AmericanExpress\HyperledgerFabricClient\Signatory\MdanterEccSignatoryFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TxIdFactory;
use AmericanExpress\HyperledgerFabricClient\ValueObject\HashAlgorithm;

class ChannelFactory
{
    /**
     * @param ClientConfigInterface $config
     * @return Channel
     */
    public static function fromConfig(ClientConfigInterface $config): Channel
    {
        $endorserClients = new EndorserClientManager();

        $transactionContextFactory = new TransactionContextFactory(
            new RandomBytesNonceGenerator($config->getIn(['nonce-size'])),
            new TxIdFactory(HashAlgorithm::fromConfig($config))
        );

        $signatory = MdanterEccSignatoryFactory::fromConfig($config);

        return new Channel($endorserClients, $transactionContextFactory, $signatory);
    }

    /**
     * @param \SplFileObject $file
     * @return Channel
     */
    public static function fromConfigFile(\SplFileObject $file)
    {
        $config = ClientConfigFactory::fromFile($file);

        return self::fromConfig($config);
    }
}
