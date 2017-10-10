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
$loader->add('', __DIR__ . "/../fabric-client/lib/");


/* Old code to be removed when above code will work properly */

/* SDK directory path */
//$dir = dirname(__FILE__).'/lib';

/**
* function to get list of all files in SDK directory
*/
/*function getFilesToInclude($rootDir, $allData=array()) {
    // set filenames invisible if you want
    $invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
    // run through content of root directory
    $dirContent = scandir($rootDir);
    foreach($dirContent as $key => $content) {
        // filter all files not accessible
        $path = $rootDir.'/'.$content;
        if(!in_array($content, $invisibleFileNames)) {
            // if content is file & readable, add to array
            if(is_file($path) && is_readable($path)) {
                // load file parts
                $file_parts = pathinfo($path);
                // check for php file
                if($file_parts["extension"]=="php"){
                    // save file name with path
                    $allData[] = $path;
                }
            // if content is a directory and readable, add path and name
            }
            elseif(is_dir($path) && is_readable($path)) {
                // recursive callback to open new directory
                $allData = getFilesToInclude($path, $allData);
            }
        }
    }
    return $allData;
}
*/
/* Getting list of all files */
//$files = getFilesToInclude($dir);

/* Loop to load all files */
/*foreach($files as $filename){
    include_once($filename);
}*/
