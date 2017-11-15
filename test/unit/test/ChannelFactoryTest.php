<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\Channel;
use AmericanExpress\HyperledgerFabricClient\ChannelFactory;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfig;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ChannelFactory
 */
class ChannelFactoryTest extends TestCase
{
    public function testFromConfig()
    {
        $result = ChannelFactory::fromConfig(new ClientConfig([]));

        self::assertInstanceOf(Channel::class, $result);
    }

    public function testFromConfigFile()
    {
        $files = vfsStream::setup('test');

        $config = vfsStream::newFile('config.php');
        $config->setContent("<?php\nreturn ['foo' => 'bar'];");
        $files->addChild($config);

        $result = ChannelFactory::fromConfigFile(new \SplFileObject($config->url()));

        self::assertInstanceOf(Channel::class, $result);
    }
}
