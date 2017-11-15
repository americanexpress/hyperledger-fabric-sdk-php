<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Config;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfig;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigFactory;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Config\ClientConfigFactory
 */
class ClientConfigFactoryTest extends TestCase
{
    public function testFromFile()
    {
        $files = vfsStream::setup('test');

        $config = vfsStream::newFile('config.php');
        $config->setContent("<?php\nreturn ['foo' => 'bar'];");
        $files->addChild($config);

        $result = ClientConfigFactory::fromFile(new \SplFileObject($config->url()));

        self::assertInstanceOf(ClientConfig::class, $result);
    }
}
