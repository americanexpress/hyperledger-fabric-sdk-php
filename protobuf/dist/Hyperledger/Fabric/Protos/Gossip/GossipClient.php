<?php
// GENERATED CODE -- DO NOT EDIT!

// Original file comments:
// Copyright IBM Corp. All Rights Reserved.
//
// SPDX-License-Identifier: Apache-2.0
//
namespace Hyperledger\Fabric\Protos\Gossip;

/**
 * Gossip
 */
class GossipClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * GossipStream is the gRPC stream used for sending and receiving messages
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GossipStream($metadata = [], $options = []) {
        return $this->_bidiRequest('/gossip.Gossip/GossipStream',
        ['\Gossip\Envelope','decode'],
        $metadata, $options);
    }

    /**
     * Ping is used to probe a remote peer's aliveness
     * @param \Google\Protobuf\GPBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Ping(\Google\Protobuf\GPBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/gossip.Gossip/Ping',
        $argument,
        ['\Google\Protobuf\GPBEmpty', 'decode'],
        $metadata, $options);
    }

}
