<?php
require __DIR__ . "/../../fabric-client/index.php";
require __DIR__ . "/e2eUtils.php";

$e2e = new E2EUtils();
$e2e->queryChaincode('org1', 'v0', '300', $t="", "");