<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: peer/configuration.proto

namespace Hyperledger\Fabric\Protos\Peer;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * AnchorPeer message structure which provides information about anchor peer, it includes host name,
 * port number and peer certificate.
 *
 * Generated from protobuf message <code>protos.AnchorPeer</code>
 */
class AnchorPeer extends \Google\Protobuf\Internal\Message
{
    /**
     * DNS host name of the anchor peer
     *
     * Generated from protobuf field <code>string host = 1;</code>
     */
    private $host = '';
    /**
     * The port number
     *
     * Generated from protobuf field <code>int32 port = 2;</code>
     */
    private $port = 0;

    public function __construct() {
        \GPBMetadata\Peer\Configuration::initOnce();
        parent::__construct();
    }

    /**
     * DNS host name of the anchor peer
     *
     * Generated from protobuf field <code>string host = 1;</code>
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * DNS host name of the anchor peer
     *
     * Generated from protobuf field <code>string host = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setHost($var)
    {
        GPBUtil::checkString($var, True);
        $this->host = $var;

        return $this;
    }

    /**
     * The port number
     *
     * Generated from protobuf field <code>int32 port = 2;</code>
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * The port number
     *
     * Generated from protobuf field <code>int32 port = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setPort($var)
    {
        GPBUtil::checkInt32($var);
        $this->port = $var;

        return $this;
    }

}

