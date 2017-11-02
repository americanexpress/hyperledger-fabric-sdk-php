<?php
declare(strict_types=1);

namespace AmericanExpressTest\Integration\Test;

use AmericanExpressTest\Integration\TestAsset\E2EUtils;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testQueryChainCode()
    {
        $e2e = new E2EUtils();
        $this->assertTrue($e2e->queryChaincode('org1') != null);
    }
}
