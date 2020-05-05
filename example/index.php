<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

/** API Sample ..  */

require __DIR__ . '/../src/Interfaces/CacheInterface.php';
require __DIR__ . '/../src/Interfaces/CacheException.php';
require __DIR__ . '/../src/CacheFactory.php';
require __DIR__ . '/../src/Drivers/Memcached.php';
require __DIR__ . '/../src/Drivers/Redis.php';
require __DIR__ . '/../src/Drivers/MongoDB.php';

use Gemblue\TinyCache\CacheFactory;

try {

    $cacheFactory = new CacheFactory;
    $cache = $cacheFactory->getInstance('MongoDB', [
        'host' => 'localhost',
        'port' => '27017',
        'persistence' => true
    ]);
    
} catch (Throwable $t) {
    echo $t->getMessage();
}

$cache->set('FOO', 'BAR', 3600);
//echo $cache->get('FOO');

// $cache->setMultiple([
//     'FOO' => 'BAR',
//     'BAR' => 'FOO'
// ], 3600);

// print_r($cache->getMultiple(['FOO', 'BAR']));

// $cache->delete('FOO');
// $cache->clear();
// $cache->deleteMultiple(['FOO', 'BAR']);
// echo $cache->has('FOO');