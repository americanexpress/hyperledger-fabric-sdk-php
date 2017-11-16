<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Transaction;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TxIdFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TxIdFactoryInterface;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Transaction\TxIdFactory
 */
class TxIdFactoryTest extends TestCase
{
    /**
     * @var TxIdFactoryInterface
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new TxIdFactory();
    }

    public function testCreateTxId()
    {
        $files = vfsStream::setup('test');

        $certs = vfsStream::newFile('foo');
        $certs->setContent('FizBuz');
        $files->addChild($certs);

        $serializedIdentity = SerializedIdentityFactory::fromBytes('FooBar', 'FizBuz');

        $result = $this->sut->fromSerializedIdentity($serializedIdentity, 'qur48f7e9');

        self::assertInternalType('string', $result);
        self::assertNotEmpty($result);
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testInvalidHashAlgorithm()
    {
        new TxIdFactory('invalidAlgorithm');
    }
}
