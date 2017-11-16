<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Serializer;

use AmericanExpress\HyperledgerFabricClient\Serializer\BinaryStringSerializer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Serializer\BinaryStringSerializer
 */
class BinaryStringSerializerTest extends TestCase
{
    /**
     * @var BinaryStringSerializer
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new BinaryStringSerializer();
    }

    public function testSerialize()
    {
        self::assertSame('AAA=', base64_encode($this->sut->serialize([
            'foo',
            'bar',
        ])));
    }

    public function testDeserialize()
    {
        self::assertSame([1 => 0, 2 => 0], $this->sut->deserialize(base64_decode('AAA=')));
    }
}
