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

        $requst = (new Channel())->getSignedProposal($fabricProposal);
        list($proposalResponse, $status) = $connect->ProcessProposal($requst)->wait();

        $status = ((array) $status);
        if (isset($status["code"]) && $status["code"] == 0) {
            print_r($proposalResponse->getPayload());
        } else {
            echo "status is not 0";
            sleep(5);
            $this->runQueryClient();
        }
    }
}