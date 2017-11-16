<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Transaction;

use AmericanExpress\HyperledgerFabricClient\ChannelContext;
use AmericanExpress\HyperledgerFabricClient\Nonce\RandomBytesNonceGenerator;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TxIdFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactory
 */
class TransactionContextFactoryTest extends TestCase
{
    /**
     * @var TransactionContextFactory
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new TransactionContextFactory(
            new RandomBytesNonceGenerator(),
            new TxIdFactory()
        );
    }

    public function testFromChannelContext()
    {
        $result = $this->sut->fromChannelContext(new ChannelContext([
            'mspId' => '1234',
            'adminCerts' => new \SplFileObject(__FILE__),
        ]));

        self::assertInstanceOf(TransactionContext::class, $result);
    }
}
