<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\ChaincodeQueryParams;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ChaincodeQueryParams
 */
class ChaincodeQueryParamsTest extends TestCase
{
    /**
     * @var ChaincodeQueryParams
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new ChaincodeQueryParams();
    }

    public function testChannelId()
    {
        self::assertNull($this->sut->getChannelId());

        $this->sut->setChannelId('MyChannelId');

        self::assertSame('MyChannelId', $this->sut->getChannelId());
    }

    public function testChaincodeName()
    {
        self::assertNull($this->sut->getChaincodeName());

        $this->sut->setChaincodeName('FooBar');

        self::assertSame('FooBar', $this->sut->getChaincodeName());
    }

    public function testChaincodePath()
    {
        self::assertNull($this->sut->getChaincodePath());

        $this->sut->setChaincodePath('FizBuz');

        self::assertSame('FizBuz', $this->sut->getChaincodePath());
    }

    public function testChaincodeVersion()
    {
        self::assertNull($this->sut->getChaincodeVersion());

        $this->sut->setChaincodeVersion('v12.34');

        self::assertSame('v12.34', $this->sut->getChaincodeVersion());
    }

    public function testArgs()
    {
        self::assertCount(0, $this->sut->getArgs());

        $this->sut->setArgs(['foo' => 'bar']);

        self::assertSame(['foo' => 'bar'], $this->sut->getArgs());
    }

    public function testFromArray()
    {
        $sut = new ChaincodeQueryParams([
            'channelId' => 'MyChannelId',
            'chaincodeName' => 'FooBar',
            'chaincodePath' => 'FizBuz',
            'chaincodeVersion' => 'v12.34',
            'args' => [
                'foo' => 'bar',
            ],
        ]);

        self::assertSame('MyChannelId', $sut->getChannelId());
        self::assertSame('FooBar', $sut->getChaincodeName());
        self::assertSame('FizBuz', $sut->getChaincodePath());
        self::assertSame('v12.34', $sut->getChaincodeVersion());
        self::assertSame(['foo' => 'bar'], $sut->getArgs());
    }
}
