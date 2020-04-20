<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

/** Sample to Try  */

if (!extension_loaded('redis')) {
    return new Exception('Redis extension is not installed or loaded.');
}

$Redis = new Redis;

$Redis->connect('localhost', '6379', '10');

$Redis->setOption($Redis::OPT_SERIALIZER,$Redis::SERIALIZER_PHP);
$set = $Redis->set('FOO', 'BAR', 1000);
$Redis->setOption($Redis::OPT_SERIALIZER, $Redis::SERIALIZER_NONE);

var_dump($set);

// Update.
$set = $Redis->set('FOO', 'BARRRRR', 1000);
var_dump($set);
// $delete = $Redis->delete('FOO');
// $Redis->flushDB();

var_dump($Redis->get('FOO'));