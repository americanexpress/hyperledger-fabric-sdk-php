# Hyperledger Fabric Client SDK for PHP
- - - - - - - - 

Welcome to PHP SDK for Hyperledger project. The main objective of this SDK is to facilitate a client to perform basic chaincode related operations like: creating a channel, installing and accessing a chaincode etc.

Note, the fabric-sdk-php is a standalone client side interface to access the network information and ledger data over running blockchain network, it cannot be used as a persistence medium for application defined channels data.

## Installation
```
composer require americanexpress/hyperledger-fabric-sdk-php
```

## Usage

Below, you will find high-level and concise snippets of code demonstrating how to interact with this SDK.

### Channel::queryByChaincode
Query first peer in first organization (default behavior):
```php
$config = new \AmericanExpress\HyperledgerFabricClient\Config\ClientConfig([
    // See `test/integration/config.php` for an example.
]);
$response = \AmericanExpress\HyperledgerFabricClient\Client\ClientFactory::fromConfig($config)
    ->getChannel('foo')
    ->getChaincode('example_cc')
    ->invoke('query', 'a');
```

Query specific organization:
```php
$config = new \AmericanExpress\HyperledgerFabricClient\Config\ClientConfig([
    // See `test/integration/config.php` for an example.
]);
$response = \AmericanExpress\HyperledgerFabricClient\Client\ClientFactory::fromConfig($config, 'peerOrg1')
    ->getChannel('foo')
    ->getChaincode('example_cc')
    ->invoke('query', 'a');
```

Query specific organization and peer:
```php
$config = new \AmericanExpress\HyperledgerFabricClient\Config\ClientConfig([
    // See `test/integration/config.php` for an example.
]);
$options = new \AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions([
    'peer' => 'peer1',
]);
$response = \AmericanExpress\HyperledgerFabricClient\Client\ClientFactory::fromConfig($config, 'peerOrg1')
    ->getChannel('foo')
    ->getChaincode('example_cc')
    ->invoke('query', 'a', $options);
```

Query chaincode by path and version:
```php
$config = new \AmericanExpress\HyperledgerFabricClient\Config\ClientConfig([
    // See `test/integration/config.php` for an example.
]);
$response = \AmericanExpress\HyperledgerFabricClient\Client\ClientFactory::fromConfig($config)
    ->getChannel('foo')
    ->getChaincode(['name' => 'example_cc', 'version' => '1', 'path' => 'github.com/example_cc'])
    ->invoke('query', 'a');
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


Before running the tests, we need to bring up the fabric network and fixture(s):
```bash
(cd ./test/fixture/sdkintegration && docker-compose up -d)
```

Stream the logs:
```bash
(cd ./test/fixture/sdkintegration && docker-compose logs -f)
```

At present we are providing example test case for Querying a chaincode, which can be run as below:
```bash
./vendor/bin/phpunit
```

After running the tests, you can bring down the fabric network:
```bash
(cd ./test/fixture/sdkintegration && docker-compose down)
```

Read more about [Docker Compose](https://docs.docker.com/compose/overview/)

## Regenerating PHP Class files from `.proto` files

```bash
composer protoc
```

Read more about [compiling PHP code from proto files](docs/compile-hyperledger-fabric-proto-files.md).

## Contributing

We welcome Your interest in the American Express Open Source Community on Github. Any Contributor to any Open Source
Project managed by the American Express Open Source Community must accept and sign an Agreement indicating agreement to
the terms below. Except for the rights granted in this Agreement to American Express and to recipients of software
distributed by American Express, You reserve all right, title, and interest, if any, in and to Your Contributions.
Please [fill out the Agreement](https://cla-assistant.io/americanexpress/fabric-sdk-php).

Please feel free to open pull requests and see `CONTRIBUTING.md` for commit formatting details.

## License

Any contributions made under this project will be governed by the [Apache License 2.0](LICENSE.txt).

## Code of Conduct

This project adheres to the [American Express Community Guidelines](CODE_OF_CONDUCT.md). By participating, you are
expected to honor these guidelines.
