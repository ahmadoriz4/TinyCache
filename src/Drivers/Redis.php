<?php

/**
 * Simple Driver for Redis.
 *
 * This class just wrap the Redis PHP Ext and follow PSR 16 Interface.
 * We don't extend Redis in this class. Just inject the class in construct.
 * Maybe in the future we change `php-redis` to other Ext.
 * 
 * @package Drivers
 * @author Gemblue
 */

namespace Gemblue\TinyCache;

class Redis implements CacheInterface
{    
    /** Ext container */
    protected $redis;
    
    /**
     * Constructor 
     * 
     * Handle connection and inject ext.
     * 
     * @return void
     */
    public function __construct(string $host, int $port, int $timeout)
    {       
        // Inject dependency.
        $this->redis = new \Redis;
        
        if (!$this->redis->connect($host, $port, $timeout)) {
            return new Exception('Failed to connect, maybe Redis server is not running, or wrong config for host and port.');
        }
    }
    
    /**
     * Get key with default if not exist.
     * 
     * @return mixed
     */
    public function get(string $key, $default = null) 
    {
        $this->redis->setOption($this->redis::OPT_SERIALIZER, $this->redis::SERIALIZER_PHP);
        
        $get = $this->redis->get($key);
        
        $this->redis->setOption($this->redis::OPT_SERIALIZER, $this->redis::SERIALIZER_NONE);
        
        if ($get == false)
            return $default;

        return $get;
    }

    /**
     * Set key, value, expire.
     * 
     * @return bool
     */
    public function set(string $key, string $value, int $ttl = null) 
    {
        $this->redis->setOption($this->redis::OPT_SERIALIZER, $this->redis::SERIALIZER_PHP);
        
        $set = $this->redis->set($key, $value, $ttl);
        
        $this->redis->setOption($this->redis::OPT_SERIALIZER, $this->redis::SERIALIZER_NONE);
        
        return $set;
    }
    
    /**
     * Delete key
     * 
     * @return bool
     */
    public function delete(string $key) 
    {
        return $this->redis->delete($key) ?? false;
    }

    /**
     * To Wipe Cache.
     * 
     * @return bool
     */
    public function clear() 
    {
        return $this->redis->flushDB();
    }

    /**
     * Get multiple Keys.
     * 
     * @return iterable
     */
    public function getMultiple(array $keys, $default = null) 
    {
        $temp = [];

        foreach ($keys as $key) {
            $temp[] = [$key => $this->redis->get($key)];
        }
        
        return $temp ?? $default;
    }

    /**
     * Set multiple key value, also with ttl.
     * 
     * @return bool
     */
    public function setMultiple(iterable $values, int $ttl = null) 
    {
        foreach ($values as $key => $value) {
            if (!$this->redis->set($key, $value, $ttl))
                return false;
        }

        return true;
    }

    /**
     * Delete multiple key.
     * 
     * @return bool
     */
    public function deleteMultiple(iterable $keys) 
    {
        foreach ($keys as $key) {
            if (!$this->redis->delete($key))
                return false;
        }

        return true;
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
        if ($this->redis->get($key))
            return true;

        return false;
    }
}