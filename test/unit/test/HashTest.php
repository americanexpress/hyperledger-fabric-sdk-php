<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\ClientConfig;
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

    public function testGetByteArray()
    {
        $actual = $this->sut->toByteArray('FooBar');

        $expected = [
            1 => 70,
            2 => 111,
            3 => 111,
            4 => 66,
            5 => 97,
            6 => 114,
        ];

        self::assertSame($expected, $actual);
    }

    public function testProposalArrayToBinaryString()
    {
        $result = $this->sut->proposalArrayToBinaryString([
            'foo',
            'bar',
        ]);

        self::assertNotEmpty($result);
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
}
