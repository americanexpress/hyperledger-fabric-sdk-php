<?php

class E2EUtils
{
  
    function queryChaincode($org, $version, $value, $t, $transientMap)
    {
        $utils = new Utils();
        $nouncevalue = $utils->getNonce();

        $connect = $utils->FabricConnect();
        
        $channel = new Channel();
        $fabricProposal = $channel->createFabricProposal($nouncevalue);

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