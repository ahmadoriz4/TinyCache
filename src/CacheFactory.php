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
     * Factory Method
     * 
     * This method responsible to generate cache object.
     *  
     * @return void
     */
    public function getInstance(string $driver, string $host, int $port, int $arg) 
    {
        if (!in_array($driver, $this->whitelists)) {
            die($driver .' extension is not supported.');
        }
        
        if (!extension_loaded(strtolower($driver))) {
            die($driver . ' extension is not installed');
        }

        // We are not using automatic instantiate like `new $driver`
        // Because of class colission, use case instead.
        switch ($driver) {
            case 'Redis':
                return new Redis($host, $port, $arg);
                break;
            case 'Memcached':
                return new Memcached($host, $port, $arg);
                break;
            default:
                die('Driver ' . $driver .' is not supported.');
        }
    }
}