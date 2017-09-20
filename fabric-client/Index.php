<?php

if(phpversion()<7){
    echo "Please upgrade your php version to 7+.";
    exit (1);
}

$loader_path = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($loader_path)) {
    echo "Dependencies must be installed using composer:\n\n";
    echo "php composer.phar install\n\n";
    echo "See http://getcomposer.org for help with installing composer\n";
    exit(1);
}
$loader = include $loader_path;

if (!extension_loaded('gmp')) {
    echo "Please install php-gmp extension:\n\n";
    echo "See http://php.net/manual/en/book.gmp.php for help with installing gmp extension\n";
    exit(1);
}

$dir = dirname(__FILE__).'/lib';

//$loader->add('', __DIR__. "/../fabric-client/lib/");
//$loader->add('', __DIR__. "/../fabric-client/lib/protosPHP");


function scanDirectories($rootDir, $allData=array()) {
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
                // save file name with path
                $allData[] = $path;
            // if content is a directory and readable, add path and name
            }elseif(is_dir($path) && is_readable($path)) {
                // recursive callback to open new directory
                $allData = scanDirectories($path, $allData);
            }
        }
    }
    return $allData;
}

$files = scanDirectories($dir);

foreach($files as $filename){
    include_once($filename);
}
