<?php
class Channel
{
    function getSignedProposal($proposal)
    {
        $signedProposal = new Protos\SignedProposal();
        $proposalString = $proposal->serializeToString();
        $signedProposal->setProposalBytes($proposalString);

        $signatureString = (new TransactionContext())->signByteString($proposal);
        $signedProposal->setSignature($signatureString);

        return $signedProposal;
    }

    function getSignatureHeaderAsByteString($protoUtils, $transactionCtxt)
    {

        $identity = $protoUtils->createSerializedIdentity($this->config['member']['admin_certs'], $this->config['member']['sample_msp_id']);
        $identitystring = $identity->serializeToString();
        $nounce = $transactionCtxt->getNonceValue();

        $signatureHeader = new Common\SignatureHeader();
        $signatureHeader->setCreator($identitystring);
        $signatureHeader->setNonce($nounce);

        $signatureHeaderString = $signatureHeader->serializeToString();

        return $signatureHeaderString;
    }

    function getTransactionId($protoUtils, $nounce){
        $common = new Common();
        return $common->getTxId($protoUtils, $nounce);
    }

    public function createFabricProposal($nouncevalue)
    {
        $clientUtils = new ClientUtils();
        $TimeStamp = $clientUtils->buildCurrentTimestamp();

        $chaincodeID = new Protos\ChaincodeID();
        $chaincodeID->setPath('github.com/sparrow_txn');
        $chaincodeID->setName('sparrow_txn_cc');
        $chaincodeID->setVersion('2');
        $channelID = "foo";
        
        $TransactionID = new TransactionID();

        $ccType = new Protos\ChaincodeSpec();
        $ccType->setType(1);//1 for GOLANG
        $ccTypeString = $ccType->serializeToString();

        $chaincodeHeaderExtension = new Protos\ChaincodeHeaderExtension();
        $chaincodeHeaderExtension->setChaincodeId($chaincodeID);

        $ENDORSER_TRANSACTION = 3;
        $txID = $TransactionID->getTxId($nouncevalue);
        $TimeStamp = $clientUtils->buildCurrentTimestamp();
        $chainHeader = new Common\ChannelHeader();
        $chainHeader = $protoUtils->createChannelHeader($ENDORSER_TRANSACTION, $txID, $channelID, 0, $TimeStamp, $chaincodeHeaderExtension);
        $chainHeaderString = $chainHeader->serializeToString();

        $chaincodeInvocationSpec = new Protos\ChaincodeInvocationSpec();
        $chaincodeInvocationSpec = $this->createChaincodeInvocationSpec($chaincodeID, $ccType);
        $chaincodeInvocationSpecString = $chaincodeInvocationSpec->serializeToString();

        $payload = new Protos\ChaincodeProposalPayload();
        $payload->setInput($chaincodeInvocationSpecString);
        //$payload->putAllTransientMap();// not available in php
        $payloadString = $payload->serializeToString();

        $header = new Common\Header();
        $getSignatureHeaderAsByteString = $channelObj->getSignatureHeaderAsByteString($protoUtils, $transactionCtxt);

        $header->setSignatureHeader($getSignatureHeaderAsByteString);
        $header->setChannelHeader($chainHeaderString);
        $headerString = $header->serializeToString();

        $proposal = new Protos\Proposal();
        $proposal->setHeader($headerString);
        $proposal->setPayload($payloadString);

        return $proposal;
    }
}