#!/bin/bash
peer channel create -o orderer.example.com:7050 -c foo -f ./channel-artifacts/foo.tx --cafile /opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/ordererOrganizations/example.com/orderers/orderer.example.com/msp/tlscacerts/ca.example.com-cert.pem
peer channel join -b foo.block
peer chaincode install -n example_cc -v 1.0 -p github.com/hyperledger/fabric/examples/chaincode/go/example_cc
peer chaincode instantiate -o orderer.example.com:7050  --cafile /opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/ordererOrganizations/example.com/orderers/orderer.example.com/msp/tlscacerts/ca.example.com-cert.pem -C foo -n example_cc -v 1.0 -c '{"Args":["init","a","500","b","700"]}' -P "OR ('Org1MSP.member','Org2MSP.member')"
while :
do
	echo "Press [CTRL+C] to stop.."
	sleep 1
done