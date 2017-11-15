<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Security;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Cryptography\MdanterEcc;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Cryptography\MdanterEcc
 */
class MdanterEccTest extends TestCase
{
    /**
     * @var MdanterEcc
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new MdanterEcc();
    }

    public function testDefaultNonceLength()
    {
        $nonce = $this->sut->getNonce();

        self::assertSame(24, strlen($nonce));
    }

    public function testConfigurableNonceLength()
    {
        $nonce = (new MdanterEcc(3))->getNonce();

        self::assertSame(3, strlen($nonce));
    }

    public function testSignByteString()
    {
        $files = vfsStream::setup('test');

        $certs = vfsStream::newFile('foo');
        $certs->setContent(<<<'TAG'
-----BEGIN PRIVATE KEY-----
MIGHAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBG0wawIBAQQghnA7rdgbZi/wndus
iXjyf0KgE6OKZjQ+5INjwelRAC6hRANCAASb3u+hY+U/FZvhYDN6d08HJ1v56UJU
yz/n2NHyJgTg6kC05AaJMeGIinEF0JeJtRDNVQGzoQJQYjnzUTS9FvGh
-----END PRIVATE KEY-----
TAG
        );
        $files->addChild($certs);

        $result = $this->sut->signByteString(new Proposal(), new \SplFileObject($certs->url()));

        self::assertInternalType('string', $result);
        self::assertNotEmpty($result);
    }

    public function testCreateTxId()
    {
        $files = vfsStream::setup('test');

        $certs = vfsStream::newFile('foo');
        $certs->setContent('FizBuz');
        $files->addChild($certs);

        $serializedIdentity = SerializedIdentityFactory::fromBytes('FooBar', 'FizBuz');

        $result = $this->sut->createTxId($serializedIdentity, 'qur48f7e9');

        self::assertInternalType('string', $result);
        self::assertNotEmpty($result);
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testInvalidNonceSize()
    {
        new MdanterEcc(-1, 'md5');
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testInvalidHashAlgorithm()
    {
        new MdanterEcc(24, 'invalidAlgorithm');
    }
}
