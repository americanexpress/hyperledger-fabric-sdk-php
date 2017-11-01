# Hyperledger Fabric Client SDK for PHP
- - - - - - - - 

Welcome to PHP SDK for Hyperledger project. The main objective of this SDK is to facilitate a client to perform basic chaincode related operations like: creating a channel, installing and accessing a chaincode etc.

Note, the fabric-sdk-php is a standalone client side interface to access the network information and ledger data over running blockchain network, it cannot be used as a persistence medium for application defined channels data.

## Installation
```
composer require americanexpress/hyperledger-fabric-sdk-php
```

## Phase 1

* For phase 1, we are providing client access for basic chaincode operations like query by chain code.
* It’s under assumption that we have a running blockchain network, with a predefined channel and an installed chaincode.
* A predefined script is provided to bring up the test network as per the test cases.



## Phase 2 (Upcoming)

* In next release we are targeting to add more chaincode operations like create channel, invoke & install etc


## Latest builds of Fabric and Fabric-ca v1.1.0

Hyperledger Fabric v1.1.0 is currently under active development.

You can clone these projects by going to the [Hyperledger repository](https://gerrit.hyperledger.org/r/#/admin/projects/).






- - - - - - -

### Prerequisites ###

#### [Latest Docker](https://docs.docker.com/engine/installation)
Check docker version (it should be 17+)

`docker --version`


#### [PHP version 7+](http://php.net/manual/en/install.php)
Check version of PHP

`php --version`


#### [PHP GMP extension](http://php.net/manual/en/gmp.installation.php)
Check PHP-GMP setup in php.ini


#### [Composer tool](https://getcomposer.org/doc/00-intro.md)
Check composer version (it should be 1.5 or plus)

`composer --version`




### Installing SDK (for development)


`git clone {fabric-sdk-php}`

`cd fabric-sdk-php`

`composer install`




### Running the End2End test case


Before running the testcase, we need to bring up the fabric network along with that example channel creation and chaincode installation is also required. We are providing a script to perform all this setup which can be locate at

`cd ./test/fixture/sdkintegration && ./init.sh`

At present we are providing example test case for Querying a chaincode, which can be run as below.

`./vendor/bin/phpunit`

## Regenerating PHP Class files from `.proto` files

Install `protoc` via `protobuf`, e.g. on OSX:
```bash
brew install protobuf
```

Run this command to generate PHP classes from `.proto` files: 
```bash
find ./fabric-client/protos -name "*.proto" -exec protoc --proto_path=./fabric-client/protos/base --php_out=./fabric-client/protos/PHP {} \;
```
