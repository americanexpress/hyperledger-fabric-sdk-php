# Hyperledger Fabric Client SDK for PHP

Welcome to Java SDK for Hyperledger project. The main objective of this SDK is to facilitate a client to perform basic chaincode related operations like: creating a channel, installing and accessing a chaincode etc.

Note, the fabric-sdk-php is a standalone client side interface to access the network information and ledger data over running blockchain network, it cannot be used as a persistence medium for application defined channels data.

## Assumptions

* For phase -1 we are targeting to provide client access for basic chaincode operations like: invoke, query etc.
* It’s under assumption that we have a running blockchain network, with a predefined channel and an installed chaincode.
* A predefined script is provided to bring up the test network as per the test cases.

<p &nbsp; />
<p &nbsp; />



## Latest builds of Fabric and Fabric-ca v1.1.0

Hyperledger Fabric v1.1.0 is currently under active development.

You can clone these projects by going to the [Hyperledger repository](https://gerrit.hyperledger.org/r/#/admin/projects/).



<p &nbsp; />
<p &nbsp; />


<p &nbsp; />
<p &nbsp; />

### Setting up the composer
 
Composer tool is a prerequisite to resolve required PHP libraries and extensions. The installation and setup can be referenced from [here](https://getcomposer.org/doc/00-intro.md).

<p &nbsp; />

### Bringing up the fabric network

//TODO

### Installing SDK

`git clone {fabric-sdk-php}`
`cd fabric-sdk-php`
`composer install`

<p &nbsp; />
<p &nbsp; />

### Running the End2End test case

At present we are providing example test case for Querying a chaincode, which can be run as below.

`php test/integration/Query.php`

<p &nbsp; />
