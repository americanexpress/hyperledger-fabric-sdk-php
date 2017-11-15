<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\MSP;

use AmericanExpress\HyperledgerFabricClient\MSP\Identity;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\MSP\Identity
 */
class IdentityTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $files;

    /**
     * @var Identity
     */
    private $sut;

    protected function setUp()
    {
        $this->files = vfsStream::setup('test');

        $this->sut = new Identity($this->files->url());
    }

    public function testCreateSerializedIdentity()
    {
        $certs = vfsStream::newFile('foo');
        $certs->setContent('FizBuz');
        $this->files->addChild($certs);

        $identity = $this->sut->createSerializedIdentity($certs->url(), 'bar');

        self::assertInstanceOf(SerializedIdentity::class, $identity);
        self::assertSame('FizBuz', $identity->getIdBytes());
        self::assertSame('bar', $identity->getMspid());
    }
}
