<?php

/**
 * Simple Driver for Memcached.
 *
 * @package Drivers
 * @author Gemblue
 */

namespace Gemblue\TinyCache;

class Memcached
{    
    /** Ext container */
    protected $memcached;
    
    /**
     * Constructor 
     * 
     * Handle connection and Inject Ext.
     * 
     * @return void
     */
    public function __construct(string $host, int $port, int $timeout)
    {       
       
    }
    
    /**
     * Get key with default if not exist.
     * 
     * @return mixed
     */
    public function get(string $key, $default = null) 
    {
        
    }

    /**
     * Set key, value, expire.
     * 
     * @return bool
     */
    public function set(string $key, string $value, int $ttl = null) 
    {
        
    }
    
    /**
     * Delete key
     * 
     * @return bool
     */
    public function delete(string $key) 
    {
        
    }

    /**
     * To Wipe Cache.
     * 
     * @return bool
     */
    public function clear() 
    {
        
    }

    /**
     * Get multiple Keys.
     * 
     * @return iterable
     */
    public function getMultiple(array $keys, $default = null) 
    {
       
    }

    /**
     * Set multiple key value, also with ttl.
     * 
     * @return bool
     */
    public function setMultiple(iterable $values, int $ttl = null) 
    {
        
    }

    /**
     * Delete multiple key.
     * 
     * @return bool
     */
    public function deleteMultiple(iterable $keys) 
    {
       
    }

    /**
     * Has
     * 
     * To check value is exist or no.
     * 
     * @return bool
     */
    public function has(string $key) 
    {
        
    }
}