<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: peer/events.proto

namespace Hyperledger\Fabric\Protos\Peer;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>protos.Interest</code>
 */
class Interest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.protos.EventType event_type = 1;</code>
     */
    private $event_type = 0;
    /**
     * Generated from protobuf field <code>string chainID = 3;</code>
     */
    private $chainID = '';
    protected $RegInfo;

    public function __construct() {
        \GPBMetadata\Peer\Events::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>.protos.EventType event_type = 1;</code>
     * @return int
     */
    public function getEventType()
    {
        return $this->event_type;
    }

    /**
     * Generated from protobuf field <code>.protos.EventType event_type = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setEventType($var)
    {
        GPBUtil::checkEnum($var, \Hyperledger\Fabric\Protos\Peer\EventType::class);
        $this->event_type = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.protos.ChaincodeReg chaincode_reg_info = 2;</code>
     * @return \Hyperledger\Fabric\Protos\Peer\ChaincodeReg
     */
    public function getChaincodeRegInfo()
    {
        return $this->readOneof(2);
    }

    /**
     * Generated from protobuf field <code>.protos.ChaincodeReg chaincode_reg_info = 2;</code>
     * @param \Hyperledger\Fabric\Protos\Peer\ChaincodeReg $var
     * @return $this
     */
    public function setChaincodeRegInfo($var)
    {
        GPBUtil::checkMessage($var, \Hyperledger\Fabric\Protos\Peer\ChaincodeReg::class);
        $this->writeOneof(2, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>string chainID = 3;</code>
     * @return string
     */
    public function getChainID()
    {
        return $this->chainID;
    }

    /**
     * Generated from protobuf field <code>string chainID = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setChainID($var)
    {
        GPBUtil::checkString($var, True);
        $this->chainID = $var;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegInfo()
    {
        return $this->whichOneof("RegInfo");
    }

}
