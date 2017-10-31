<?php
// GENERATED CODE -- DO NOT EDIT!

// Original file comments:
//
// Copyright IBM Corp. 2016 All Rights Reserved.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
// http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
namespace Hyperledger\Fabric\Protos\Orderer;

/**
 */
class AtomicBroadcastClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * broadcast receives a reply of Acknowledgement for each common.Envelope in order, indicating success or type of failure
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Broadcast($metadata = [], $options = []) {
        return $this->_bidiRequest('/orderer.AtomicBroadcast/Broadcast',
        ['\Hyperledger\Fabric\Protos\Orderer\BroadcastResponse','decode'],
        $metadata, $options);
    }

    /**
     * deliver first requires an Envelope of type DELIVER_SEEK_INFO with Payload data as a mashaled SeekInfo message, then a stream of block replies is received.
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Deliver($metadata = [], $options = []) {
        return $this->_bidiRequest('/orderer.AtomicBroadcast/Deliver',
        ['\Hyperledger\Fabric\Protos\Orderer\DeliverResponse','decode'],
        $metadata, $options);
    }

}
