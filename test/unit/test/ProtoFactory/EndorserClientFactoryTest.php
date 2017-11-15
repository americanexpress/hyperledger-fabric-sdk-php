<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\EndorserClientFactory;
use Grpc\ChannelCredentials;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\EndorserClientFactory
 */
class EndorserClientFactoryTest extends TestCase
{
    public function testFromInsecureChannelCredentials()
    {
        $endorserClient = EndorserClientFactory::fromInsecureChannelCredentials('example.com');

        self::assertInstanceOf(EndorserClient::class, $endorserClient);
    }

    public function testFromChannelCredentials()
    {
        $endorserClient = EndorserClientFactory::fromChannelCredentials(
            'example.com',
            ChannelCredentials::createInsecure()
        );

        self::assertInstanceOf(EndorserClient::class, $endorserClient);
    }
}
