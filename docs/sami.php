<?php

return new Sami\Sami(dirname(__DIR__) . '/src', [
    'build_dir' => dirname(__DIR__) . '/build/docs',
    'cache_dir' => dirname(__DIR__) . '/build/docs-cache',
]);
