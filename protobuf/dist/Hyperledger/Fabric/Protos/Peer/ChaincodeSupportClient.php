<?php
// GENERATED CODE -- DO NOT EDIT!

// Original file comments:
//
// Copyright IBM Corp. 2017 All Rights Reserved.
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
namespace Hyperledger\Fabric\Protos\Peer;

/**
 * Interface that provides support to chaincode execution. ChaincodeContext
 * provides the context necessary for the server to respond appropriately.
 */
class ChaincodeSupportClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Register($metadata = [], $options = []) {
        return $this->_bidiRequest('/protos.ChaincodeSupport/Register',
        ['\Hyperledger\Fabric\Protos\Peer\ChaincodeMessage','decode'],
        $metadata, $options);
    }

}
