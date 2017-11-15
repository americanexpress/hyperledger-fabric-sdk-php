<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\ClientConfig;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ClientConfig
 */
class ClientConfigTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $files;

    /**
     * @var ClientConfig
     */
    private $sut;

    protected function setUp()
    {
        $this->files = vfsStream::setup('test');

        $this->sut = new ClientConfig([
            'foo' => [
                'bar' => 'FizBuz',
            ],
        ]);
    }

    public function testGetIn()
    {
        self::assertSame('FizBuz', $this->sut->getIn(['foo', 'bar']));
        self::assertSame(['bar' => 'FizBuz'], $this->sut->getIn(['foo']));
        self::assertSame(null, $this->sut->getIn(['Alice', 'Bob']));
        self::assertSame('FizBuz', $this->sut->getIn(['Alice', 'Bob'], 'FizBuz'));
    }

    public function testGetDefaults()
    {
        $sut = new ClientConfig([]);

        self::assertSame(5000, $sut->getIn(['timeout']));
        self::assertSame(0, $sut->getIn(['epoch']));
        self::assertSame('sha256', $sut->getIn(['crypto-hash-algo']));
        self::assertSame(24, $sut->getIn(['nonce-size']));
    }

    public function testOverrideDefaults()
    {
        $sut = new ClientConfig([
            'timeout' => 10,
            'epoch' => -100,
            'crypto-hash-algo' => 'md5',
            'nonce-size'  => 3,
        ]);

        self::assertSame(10, $sut->getIn(['timeout']));
        self::assertSame(-100, $sut->getIn(['epoch']));
        self::assertSame('md5', $sut->getIn(['crypto-hash-algo']));
        self::assertSame(3, $sut->getIn(['nonce-size']));
    }
}
