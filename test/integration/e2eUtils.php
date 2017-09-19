<?php

use org\amex\fabric_client;

class E2EUtils
{
  
   public function queryChaincode($org, $version, $value, $t, $transientMap)
    {
        $utils = new fabric_client\Utils();

//        $nounce = $utils::getNonce();

        $connect = $utils->FabricConnect();
        
        $channel = new org\amex\fabric_client\Channel();

        $fabricProposal = $channel->queryByChainCode($connect);


    }
}