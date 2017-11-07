<?php

namespace AmericanExpressTest\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\ClientConfig;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
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
        self::assertSame(['foo' => ['bar' => 'FizBuz']], $this->sut->getIn([]));
        self::assertSame(['foo' => ['bar' => 'FizBuz']], $this->sut->getIn());
        self::assertSame(null, $this->sut->getIn(['Alice', 'Bob']));
        self::assertSame('FizBuz', $this->sut->getIn(['Alice', 'Bob'], 'FizBuz'));
    }

    public function testGetDefault()
    {
        self::assertSame(5000, $this->sut->getDefault('timeout'));
        self::assertSame(0, $this->sut->getDefault('epoch'));
        self::assertSame('sha256', $this->sut->getDefault('crypto-hash-algo'));
        self::assertSame(24, $this->sut->getDefault('nonce-size'));
        self::assertSame(null, $this->sut->getDefault('FooBar'));
    }

    public function testCreateFromValidJsonFile()
    {
        $file = vfsStream::newFile('json');
        $file->setContent(json_encode([
            'foo' => [
                'bar' => 'FizBuz',
            ],
        ]));
        $this->files->addChild($file);

        $config = ClientConfig::createFromJsonFile($file->url());

        self::assertSame('FizBuz', $config->getIn(['foo', 'bar']));
    }

    public function testCreateFromValidJsonFileAtRootPath()
    {
        $file = vfsStream::newFile('json');
        $file->setContent(json_encode([
            'foo' => [
                'bar' => 'FizBuz',
            ],
        ]));
        $this->files->addChild($file);

        $config = ClientConfig::createFromJsonFile('json', $this->files->url());

        self::assertSame('FizBuz', $config->getIn(['foo', 'bar']));
    }

    public function testCreateFromInvalidJsonFile()
    {
        $file = vfsStream::newFile('json');
        $file->setContent('InvalidJsonString');
        $this->files->addChild($file);

        $config = ClientConfig::createFromJsonFile($file->url());

        self::assertSame(null, $config->getIn(['foo', 'bar']));
    }
}
