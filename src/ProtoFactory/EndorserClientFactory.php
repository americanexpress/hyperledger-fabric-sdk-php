<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\ProtoFactory;

use Grpc\ChannelCredentials;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;

class EndorserClientFactory
{
    /**
     * @param string $host
     * @return EndorserClient
     */
    public static function fromInsecureChannelCredentials(string $host): EndorserClient
    {
        $channelCredentials = ChannelCredentials::createInsecure();

        return self::fromChannelCredentials($host, $channelCredentials);
    }

    /**
     * @param string $host
     * @param ChannelCredentials $channelCredentials
     * @return EndorserClient
     */
    public static function fromChannelCredentials(
        string $host,
        ChannelCredentials $channelCredentials = null
    ): EndorserClient {
        return new EndorserClient($host, [
            'credentials' => $channelCredentials,
        ]);
    }
}
