<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\ClientConfig;
use AmericanExpress\HyperledgerFabricClient\Factory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Hash;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Hash
 */
class HashTest extends TestCase
{
    /**
     * @var Hash
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new Hash(new ClientConfig([]));
    }

    public function testDefaultNonceLength()
    {
        $nonce = $this->sut->getNonce();

        self::assertSame(24, strlen($nonce));
    }

    public function testConfigurableNonceLength()
    {
        $nonce = (new Hash(new ClientConfig(['nonce-size' => 3])))->getNonce();

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

        $sut = new Hash(new ClientConfig([
            'MyNetwork' => [
                'MyOrg' => [
                    'private_key' => $certs->url(),
                ],
            ],
        ]));

        $result = $sut->signByteString(new Proposal(), 'MyOrg', 'MyNetwork');

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
}
