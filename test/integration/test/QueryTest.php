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
        $result = $e2e->queryChaincode('org1');
        $this->assertTrue($result != null);
    }
}
