<?php
$loader = require __DIR__ . "/../vendor/autoload.php";

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
