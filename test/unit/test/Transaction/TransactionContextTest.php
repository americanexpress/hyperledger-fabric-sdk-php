<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Transaction;

use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext
 */
class TransactionContextTest extends TestCase
{
    public function testValues()
    {
        $serializedIdentity = new SerializedIdentity();
        $nonce = 'u4i6o2j6n6';
        $txId = 'i3o6kf8t0ek';
        $epoch = 54321;

        $sut = new TransactionContext($serializedIdentity, $nonce, $txId, $epoch);

        self::assertSame($serializedIdentity, $sut->getSerializedIdentity());
        self::assertSame($nonce, $sut->getNonce());
        self::assertSame($txId, $sut->getTxId());
        self::assertSame($epoch, $sut->getEpoch());
    }
}
