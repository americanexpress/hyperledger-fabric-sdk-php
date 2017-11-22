<?php

/**
 * Copyright 2017 American Express Travel Related Services Company, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
 * or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

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
