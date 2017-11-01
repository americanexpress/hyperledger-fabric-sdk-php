# Compile Hyperledger-fabric proto files for PHP

Authors: Jitendra Singh Dikhit & Ashish Kumar
	
## Introduction

In this blog tutorial, we are going to setup protocol buffer compiler and gRPC extension for PHP language. After this, you will be able to generate proto buffer files with gRPC support for Hyperledger-Fabric proto files in PHP language.

### What are protocol Buffers?

Protocol Buffers (a.k.a., protobuf) are Google's language-neutral, platform-neutral, extensible mechanism for serializing structured data, useful in developing languages to communicate with each other. Protocol buffers are designed to emphasize simplicity and performance.

Read more about [Google protobuf](https://developers.google.com/protocol-buffers/)

### What is gRPC?

gRPC is a client application, which can directly call methods on a server application on a different machine as if it was a local object, making it easier for you to create distributed applications and services.

Read more about [gRPC](https://grpc.io/)

### What is Hyperledger-Fabric

It is a business blockchain framework hosted by the Linux Foundation intended as a foundation for developing blockchain applications or solutions with a modular architecture. It is a platform for distributed ledger solutions with high degree of confidentiality, resiliency, flexibility, and scalability.

Read more about [Hyperledger](http://hyperledger.org/)

### Why PHP?

PHP is a great option for many reasons; here are some reasons why the language may be right for you or your project:
1.	It's free and simple.
1.	Powerful, flexible, and scalable.
1.	Fast loading.
1.	Large open source support.
1.	Extensions and add-ons.
1.	Exceptional performance.

## Installation

This installation showcase is performed on a Mac machine, which has brew installed. We are going to install PHP version 7.1 because protocol buffers only support up to version 7.1 thusfar.

Step 1: Install PHP 7.1 via following brew commands.
```bash
brew search php71
brew install homebrew/php/php71
```

Step 2: Install `php-grpc` extension via following brew commands.
```bash
brew search grpc
brew install grpc
brew install homebrew/php/php71-grpc
```

Step 3: Install protobuf compiler via following brew commands.
```bash
brew search protobuf
brew install protobuf
brew install homebrew/php/php71-protobuf
```

Step 4: Check your current version of PHP via `php -version` and if it's not php71, unlink your current version via `brew unlink php56`.

Now link your 7.1 version of PHP `brew link php71` and check your version again.

Step 5: Check that the protobuf compiler installed properly, `protoc -version`.

Step 6: Create a directory named `protos` and download `helloworld.proto` file inside it; download sample proto file from [helloworld.proto](https://raw.githubusercontent.com/grpc/grpc-go/master/examples/helloworld/helloworld/helloworld.proto).

Commands to run:

```bash
mkdir protos
touch protos/helloworld.proto
```

Step 7: Create a `php` directory: `mkdir php`

Step 8: Check for the grpc-php plugin path at `/usr/local/bin/grpc_php_plugin`

Step 9: Generate `grpc-protobuf-php` files from proto file:
```bash
protoc --proto_path=protos/ --php_out=php/ --grpc_out=php/ --plugin=protoc-gen-grpc=/usr/local/bin/grpc_php_plugin protos/helloworld.proto
```

Additional commands on the [PHP/gRPC site](https://grpc.io/docs/tutorials/basic/php.html).

Step 10: Check that your generated folder structure is similar to:
tree php/
```text
Output: php/
├── GPBMetadata
│   └── Helloworld.php
└── Helloworld
    ├── GreeterClient.php
    ├── HelloReply.php
    └── HelloRequest.php
```

Now, you are all setup for generating PHP files for Hyperledger-fabric proto files.

## Compile Hyperledger-fabric proto files

Hyperledger-fabric provides proto files to generate gRPC files in the supported language. We will download standard proto files given by Hyperledger community and compile them.

Step 1: Get the [proto folder](https://github.com/hyperledger/fabric-sdk-node/tree/release/fabric-client/lib/protos).

Step 2: Run above with `protoc` or create a bash file with the code to generate.
```bash
# Generate PHP files for protos of common directory.
protoc --proto_path=protos/ \
        --php_out=php/ \
        --grpc_out=php/ \
        --plugin=protoc-gen-grpc=/usr/local/bin/grpc_php_plugin \
        protos/common/common.proto \
        protos/common/configtx.proto \
        protos/common/configuration.proto \
        protos/common/ledger.proto \
        protos/common/policies.proto

protoc --proto_path=protos/ \
        --php_out=php/ \
        --grpc_out=php/ \
        --plugin=protoc-gen-grpc=/usr/local/bin/grpc_php_plugin \
        protos/google/protobuf/empty.proto \
        protos/google/protobuf/timestamp.proto 

# Generate PHP files for protos of msp directory.
protoc --proto_path=protos/ \
        --php_out=php/ \
        --grpc_out=php/ \
        --plugin=protoc-gen-grpc=/usr/local/bin/grpc_php_plugin \
        protos/msp/identities.proto \
        protos/msp/msp_config.proto \
        protos/msp/msp_principal.proto


# Generate PHP files for protos of orderer directory.
protoc --proto_path=protos/ \
        --php_out=php/ \
        --grpc_out=php/ \
        --plugin=protoc-gen-grpc=/usr/local/bin/grpc_php_plugin \
        protos/orderer/ab.proto \
        protos/orderer/configuration.proto


# Generate PHP files for protos of peer directory.
protoc --proto_path=protos/ \
        --php_out=php/ \
        --grpc_out=php/ \
        --plugin=protoc-gen-grpc=/usr/local/bin/grpc_php_plugin \
        protos/peer/admin.proto \
        protos/peer/chaincode_event.proto \
        protos/peer/chaincode_shim.proto \
        protos/peer/chaincode.proto \
        protos/peer/configuration.proto \
        protos/peer/events.proto \
        protos/peer/peer.proto \
        protos/peer/proposal_response.proto \
        protos/peer/proposal.proto \
        protos/peer/query.proto \
        protos/peer/signed_cc_dep_spec.proto \
        protos/peer/transaction.proto
```

## Conclusion

Check generated files with `tree protobuf/dist/`

All done!!! These files are ready to include and communicate with Hyperledger-fabric server.

## References

* [http://hyperledger.org/](http://hyperledger.org/)
* [https://grpc.io/](https://grpc.io/)
* [https://developers.google.com/protocol-buffers/](https://developers.google.com/protocol-buffers/)
