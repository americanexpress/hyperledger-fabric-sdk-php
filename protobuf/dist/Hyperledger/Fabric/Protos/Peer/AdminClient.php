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
namespace Hyperledger\Fabric\Protos\Peer;

/**
 * Interface exported by the server.
 */
class AdminClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Return the serve status.
     * @param \Google\Protobuf\GPBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetStatus(\Google\Protobuf\GPBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/protos.Admin/GetStatus',
        $argument,
        ['\Hyperledger\Fabric\Protos\Peer\ServerStatus', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Google\Protobuf\GPBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function StartServer(\Google\Protobuf\GPBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/protos.Admin/StartServer',
        $argument,
        ['\Hyperledger\Fabric\Protos\Peer\ServerStatus', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Hyperledger\Fabric\Protos\Peer\LogLevelRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetModuleLogLevel(\Hyperledger\Fabric\Protos\Peer\LogLevelRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/protos.Admin/GetModuleLogLevel',
        $argument,
        ['\Hyperledger\Fabric\Protos\Peer\LogLevelResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Hyperledger\Fabric\Protos\Peer\LogLevelRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function SetModuleLogLevel(\Hyperledger\Fabric\Protos\Peer\LogLevelRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/protos.Admin/SetModuleLogLevel',
        $argument,
        ['\Hyperledger\Fabric\Protos\Peer\LogLevelResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Google\Protobuf\GPBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function RevertLogLevels(\Google\Protobuf\GPBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/protos.Admin/RevertLogLevels',
        $argument,
        ['\Google\Protobuf\GPBEmpty', 'decode'],
        $metadata, $options);
    }

}
