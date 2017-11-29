#!/usr/bin/env bash

# Copyright 2017 American Express Travel Related Services Company, Inc.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
# http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
# or implied. See the License for the specific language governing
# permissions and limitations under the License.

# Dynamically compile `.proto` files into PHP classes.
find protobuf/protos -name '*.proto' -exec protoc --proto_path=protobuf/protos/ --proto_path=vendor/google/protobuf/src/google/protobuf --php_out=protobuf/dist/ --grpc_out=protobuf/dist/ --plugin=protoc-gen-grpc=/usr/local/bin/grpc_php_plugin {} \;

# `\Hyperledger\Fabric\Protos\Orderer\AtomicBroadcastClient` is incorrectly generated with references to `\Orderer\BroadcastResponse`.
find protobuf/dist -name '*.php' -exec sed -i '' -e 's/\\Orderer\\BroadcastResponse/\\Hyperledger\\Fabric\\Protos\\Orderer\\BroadcastResponse/g' {} \;

# `\Hyperledger\Fabric\Protos\Orderer\AtomicBroadcastClient` is incorrectly generated with references to `\Orderer\DeliverResponse`.
find protobuf/dist -name '*.php' -exec sed -i '' -e 's/\\Orderer\\DeliverResponse/\\Hyperledger\\Fabric\\Protos\\Orderer\\DeliverResponse/g' {} \;

# `\Hyperledger\Fabric\Protos\Peer\AdminClient` is incorrectly generated with references to `\Protos\ServerStatus`.
find protobuf/dist -name '*.php' -exec sed -i '' -e 's/\\Protos\\ServerStatus/\\Hyperledger\\Fabric\\Protos\\Peer\\ServerStatus/g' {} \;

# `\Hyperledger\Fabric\Protos\Peer\AdminClient` is incorrectly generated with references to `\Protos\LogLevelRequest`.
find protobuf/dist -name '*.php' -exec sed -i '' -e 's/\\Protos\\LogLevelRequest/\\Hyperledger\\Fabric\\Protos\\Peer\\LogLevelRequest/g' {} \;

# `\Hyperledger\Fabric\Protos\Peer\AdminClient` is incorrectly generated with references to `\Protos\LogLevelResponse`.
find protobuf/dist -name '*.php' -exec sed -i '' -e 's/\\Protos\\LogLevelResponse/\\Hyperledger\\Fabric\\Protos\\Peer\\LogLevelResponse/g' {} \;

# `\Hyperledger\Fabric\Protos\Peer\AdminClient` is incorrectly generated with references to `\Google\Protobuf\Empty`.
find protobuf/dist -name '*.php' -exec sed -i '' -e 's/\\Google\\Protobuf\\Empty/\\Google\\Protobuf\\GPBEmpty/g' {} \;

# `\Hyperledger\Fabric\Protos\Peer\ChaincodeSupportClient` is incorrectly generated with references to `\Protos\ChaincodeMessage`.
find protobuf/dist -name '*.php' -exec sed -i '' -e 's/\\Protos\\ChaincodeMessage/\\Hyperledger\\Fabric\\Protos\\Peer\\ChaincodeMessage/g' {} \;

# `\Hyperledger\Fabric\Protos\Peer\EndorserClient` is incorrectly generated with references to `\Protos\ProposalResponse`.
find protobuf/dist -name '*.php' -exec sed -i '' -e 's/\\Protos\\ProposalResponse/\\Hyperledger\\Fabric\\Protos\\Peer\\ProposalResponse/g' {} \;

# `\Hyperledger\Fabric\Protos\Peer\EventsClient` is incorrectly generated with references to `\Protos\Event`.
find protobuf/dist -name '*.php' -exec sed -i '' -e 's/\\Protos\\Event/\\Hyperledger\\Fabric\\Protos\\Peer\\Event/g' {} \;

# `\Hyperledger\Fabric\Protos\Peer\ChaincodeMessage` and `\Hyperledger\Fabric\Protos\Peer\EndorserClient` are incorrectly generated with references to `\Protos\SignedProposal`.
find protobuf/dist -name '*.php' -exec sed -i '' -e 's/\\Protos\\SignedProposal/\\Hyperledger\\Fabric\\Protos\\Peer\\SignedProposal/g' {} \;
