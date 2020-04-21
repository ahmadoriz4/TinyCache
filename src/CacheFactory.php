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
        'Redis' => \Gemblue\TinyCache\Drivers\Redis::class,
        'Memcached' => \Gemblue\TinyCache\Drivers\Memcached::class,
    ];

    /**
     * Factory Method
     * 
     * This method responsible to generate cache object.
     *  
     * @return void
     */
    public function getInstance(string $driver, string $host, int $port, int $arg) 
    {
        if (!in_array($driver, array_keys($this->whitelists))) {
            die($driver .' extension is not supported.');
        }
        
        if (!extension_loaded(strtolower($driver))) {
            die($driver . ' extension is not installed');
        }

        return new $this->whitelists[$driver]($host, $port, $arg);
    }
}
