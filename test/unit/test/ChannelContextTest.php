<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\ChannelContext;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ChannelContext
 */
class ChannelContextTest extends TestCase
{
    /**
     * @var ChannelContext
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new ChannelContext();
    }

    public function testHost()
    {
        self::assertNull($this->sut->getHost());

        $this->sut->setHost('example.com');

        self::assertSame('example.com', $this->sut->getHost());
    }

    public function testMspId()
    {
        self::assertNull($this->sut->getMspId());

        $this->sut->setMspId('1234');

        self::assertSame('1234', $this->sut->getMspId());
    }

    public function testAdminCerts()
    {
        self::assertNull($this->sut->getAdminCerts());

        $file = new \SplFileObject(__FILE__);

        $this->sut->setAdminCerts($file);

        self::assertSame($file, $this->sut->getAdminCerts());
    }

    public function testEpoch()
    {
        self::assertSame(0, $this->sut->getEpoch());

        $this->sut->setEpoch(54321);

        self::assertSame(54321, $this->sut->getEpoch());
    }

    public function testPrivateKey()
    {
        self::assertNull($this->sut->getPrivateKey());

        $file = new \SplFileObject(__FILE__);

        $this->sut->setPrivateKey($file);

        self::assertSame($file, $this->sut->getPrivateKey());
    }

    public function testFromArray()
    {
        $file = new \SplFileObject(__FILE__);

        $sut = new ChannelContext([
            'host' => 'example.com',
            'mspId' => '1234',
            'adminCerts' => $file,
            'epoch' => 54321,
            'privateKey' => $file,
        ]);

        self::assertSame('example.com', $sut->getHost());
        self::assertSame('1234', $sut->getMspId());
        self::assertSame($file, $sut->getAdminCerts());
        self::assertSame(54321, $sut->getEpoch());
        self::assertSame($file, $sut->getPrivateKey());
    }
}
