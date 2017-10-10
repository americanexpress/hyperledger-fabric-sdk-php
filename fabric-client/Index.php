<?php

define('ROOTPATH', __DIR__);

/**
* Check for latest version of PHP
*/
if(phpversion()<7){
    echo "Please upgrade your php version to 7+.";
    exit (1);
}

/**
* Check for autoloader, if not setup asking user to install composer
*/
$autoloader_path = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($autoloader_path)) {
    echo "Dependencies must be installed using composer:\n\n";
    echo "php composer.phar install\n\n";
    echo "See http://getcomposer.org for help with installing composer\n";
    exit(1);
}
$loader = include $autoloader_path;

/**
* Check for php-gmp extension
*/
if (!extension_loaded('gmp')) {
    echo "Please install php-gmp extension:\n\n";
    echo "See http://php.net/manual/en/book.gmp.php for help with installing gmp extension\n";
    exit(1);
}

/**
* Loading source and protobuf PHP files via autoloader
*/
$loader->add('', __DIR__. "/../fabric-client/protos/PHP/");
$loader->add('', __DIR__ . "/../fabric-client/src/lib/");
