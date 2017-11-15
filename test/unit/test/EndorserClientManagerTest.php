<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\EndorserClientManager;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\EndorserClientManager
 */
class EndorserClientManagerTest extends TestCase
{
    /**
     * @var EndorserClientManager
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new EndorserClientManager();
    }

    public function testGet()
    {
        $endorserClient = $this->sut->get('example.com');

        self::assertInstanceOf(EndorserClient::class, $endorserClient);
        self::assertSame($endorserClient, $this->sut->get('example.com'));
    }
}
