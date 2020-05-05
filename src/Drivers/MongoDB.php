<?php

/**
 * Simple Driver for MongoDB.
 *
 * This class just wrap the Redis PECL driver.
 * Install : sudo pecl install mongodb 
 * 
 * @package Drivers
 * @author Gemblue
 */

namespace Gemblue\TinyCache\Drivers;

use Gemblue\TinyCache\Interfaces\CacheInterface;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Exception;
use MongoDB\Driver\BulkWrite;
use MongoDB\BSON\ObjectID;

class MongoDB implements CacheInterface
{    
    /** Ext container */
    protected $mongodb;
    protected $bulkWrite;
    
    /**
     * Constructor 
     * 
     * Handle connection and inject ext.
     * 
     * @return void
     */
    public function __construct(array $options)
    {       
        // Inject dependency.
        try {
            $this->mongodb = new Manager('mongodb://'. $options['host'] .': '. $options['port']);
            $this->bulkWrite = new BulkWrite;
        } catch (Exception $e) {
            return new \Exception('Failed to connect, maybe MongoDB server is not running, or wrong config for host and port.');
        }
    }
    
    /**
     * Get key with default if not exist.
     * 
     * @return mixed
     */
    public function get(string $key, $default = null) 
    {
        $this->mongodb->setOption($this->mongodb::OPT_SERIALIZER, $this->mongodb::SERIALIZER_PHP);
        
        $get = $this->mongodb->get($key);
        
        $this->mongodb->setOption($this->mongodb::OPT_SERIALIZER, $this->mongodb::SERIALIZER_NONE);
        
        if ($get == false)
            return $default;

        return unserialize($get);
    }

    /**
     * Set key, value, expire.
     * 
     * @return bool
     */
    public function set(string $key, $value, int $ttl = null) 
    {
        $this->bulkWrite->insert(['_id' => new ObjectID, $key => serialize($value)]);
        $write = $this->mongodb->executeBulkWrite('cache.items', $bulk);
        
        print_r($write);
        exit;

        return $set;
    }
    
    public function delete(string $key) {}
    public function clear() {}
    public function getMultiple(array $keys, $default = null) {}
    public function setMultiple(array $values, int $ttl = null) {}
    public function deleteMultiple(array $keys) {}
    public function has(string $key) {}
}