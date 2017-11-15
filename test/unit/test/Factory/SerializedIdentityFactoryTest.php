<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\MSP;

use AmericanExpress\HyperledgerFabricClient\Factory\SerializedIdentityFactory;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Factory\SerializedIdentityFactory
 */
class SerializedIdentityFactoryTest extends TestCase
{
    public function testFromBytes()
    {
        $result = SerializedIdentityFactory::fromBytes('FooBar', 'FizBuz');

        self::assertInstanceOf(SerializedIdentity::class, $result);
        self::assertSame('FooBar', $result->getMspid());
        self::assertSame('FizBuz', $result->getIdBytes());
    }

    public function testFromFile()
    {
        $files = vfsStream::setup('test');

        $certs = vfsStream::newFile('foo');
        $certs->setContent('FizBuz');
        $files->addChild($certs);

        $result = SerializedIdentityFactory::fromFile('FooBar', new \SplFileObject($certs->url()));

        self::assertInstanceOf(SerializedIdentity::class, $result);
        self::assertSame('FooBar', $result->getMspid());
        self::assertSame('FizBuz', $result->getIdBytes());
    }
}
