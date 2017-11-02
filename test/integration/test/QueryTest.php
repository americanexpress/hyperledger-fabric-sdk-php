<?php

class QueryTest extends PHPUnit\Framework\TestCase
{

    public function testQueryChainCode()
    {
        $e2e = new E2EUtils();
        $this->assertTrue($e2e->queryChaincode('org1') != null);
    }
}
