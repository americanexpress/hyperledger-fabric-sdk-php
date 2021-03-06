<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: peer/transaction.proto

namespace Hyperledger\Fabric\Protos\Peer;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * TransactionAction binds a proposal to its action.  The type field in the
 * header dictates the type of action to be applied to the ledger.
 *
 * Generated from protobuf message <code>protos.TransactionAction</code>
 */
class TransactionAction extends \Google\Protobuf\Internal\Message
{
    /**
     * The header of the proposal action, which is the proposal header
     *
     * Generated from protobuf field <code>bytes header = 1;</code>
     */
    private $header = '';
    /**
     * The payload of the action as defined by the type in the header For
     * chaincode, it's the bytes of ChaincodeActionPayload
     *
     * Generated from protobuf field <code>bytes payload = 2;</code>
     */
    private $payload = '';

    public function __construct() {
        \GPBMetadata\Peer\Transaction::initOnce();
        parent::__construct();
    }

    /**
     * The header of the proposal action, which is the proposal header
     *
     * Generated from protobuf field <code>bytes header = 1;</code>
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * The header of the proposal action, which is the proposal header
     *
     * Generated from protobuf field <code>bytes header = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setHeader($var)
    {
        GPBUtil::checkString($var, False);
        $this->header = $var;

        return $this;
    }

    /**
     * The payload of the action as defined by the type in the header For
     * chaincode, it's the bytes of ChaincodeActionPayload
     *
     * Generated from protobuf field <code>bytes payload = 2;</code>
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * The payload of the action as defined by the type in the header For
     * chaincode, it's the bytes of ChaincodeActionPayload
     *
     * Generated from protobuf field <code>bytes payload = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setPayload($var)
    {
        GPBUtil::checkString($var, False);
        $this->payload = $var;

        return $this;
    }

}

