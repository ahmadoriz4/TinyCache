<?php

/**
 * TinyCache
 *
 * Main class for using TinyCache, instantiate this class and choose adaptee/driver.
 * This codes is using adapter pattern.
 * 
 * @package Factory
 * @author Gemblue
 */

namespace Gemblue\TinyCache;

class CacheFactory
{ 
    /** Driver whitelist .. */
    public $whitelists = [
        'Redis', 
        'Memcached'
    ];

    /**
     * Voila!
     * 
     * Factory pattern, create concrete Object by Driver.
     * 
     * @return void
     */
    public function getInstance(string $driver, string $host, int $port, int $timeout) 
    {
        if (!in_array($driver, $this->whitelists)) {
            die('Driver ' . $driver .' is not supported.');
        }

        // We are not using maging automatic instantiate like this new $driver.
        // There is a class colission, use case instead.
        switch ($driver) {
            case 'Redis':
                return new Redis($host, $port, $timeout);
                break;
            case 'Memcached':
                return new Memcached($host, $port, $timeout);
                break;
            default:
                die('Driver ' . $driver .' is not supported.');
        }
    }
}