<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

/** API Sample ..  */

require __DIR__ . '/../src/CacheFactory.php';
require __DIR__ . '/../src/Drivers/Memcached.php';
require __DIR__ . '/../src/Drivers/Redis.php';

use Gemblue\TinyCache\CacheFactory;

$cacheFactory = new CacheFactory;
$cache = $cacheFactory->getInstance('Redis', 'localhost', '6379', '10');
$cache->setMultiple([
    'NAME' => 'BUDI',
    'ADDRESS' => 'BANDUNG'
], 3600);

// $cache->delete('NAME');
// $cache->clear();
// $cache->deleteMultiple(['NAME', 'ADDRESS']);
// $cache->has('NAME'));

print_r($cache->getMultiple(['NAME', 'ADDRESS']));