<?php

/**
 * Simple Driver for MongoDB.
 *
 * This class just wrap the MongoDB PECL driver.
 * Install Dependency : sudo pecl install mongodb 
 * 
 * @package Drivers
 * @author Gemblue
 */

namespace Gemblue\TinyCache\Drivers;

use Gemblue\TinyCache\Interfaces\CacheInterface;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Exception;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;
use MongoDB\BSON\ObjectID;

class MongoDB implements CacheInterface
{    
    /** Ext container */
    protected $manager;
    protected $bulkWrite;
    protected $collection;
    
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
        
        // Define collection.
        $this->collection = 'cache.items';
    }
    
    /**
     * Get key with default if not exist.
     * 
     * @return mixed
     */
    public function get(string $key, $default = null) 
    {
        $query = new Query([ 'key' => $key ]);     
        $get = $this->mongodb->executeQuery($this->collection, $query)->toArray();
        
        if ($get == false)
            return $default;
        
        $get = current($get);
        
        return unserialize($get->value);
    }

    /**
     * Set key, value, expire.
     * 
     * @return bool
     */
    public function set(string $key, $value, int $ttl = null) 
    {
        // Insert new one.
        $this->bulkWrite->insert(['_id' => new ObjectID, 'key' => $key, 'value' => serialize($value)]);
        $set = $this->mongodb->executeBulkWrite($this->collection, $this->bulkWrite);
        
        return $set;
    }
    
    /**
     * Delete key
     * 
     * @return bool
     */
    public function delete(string $key) 
    {
        $this->bulkWrite->delete(['key' => $key]);
        $delete = $this->mongodb->executeBulkWrite($this->collection, $this->bulkWrite);

        return $delete ?? false;
    }

    /**
     * To Wipe Cache.
     * 
     * @return bool
     */
    public function clear() 
    {
        // Not yet.
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
            $temp[] = [$key => $this->get($key)];
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
            if (!$this->bulkWrite->insert(['_id' => new ObjectID, 'key' => $key, 'value' => serialize($value)]))
                return false;
        }

        if ($this->mongodb->executeBulkWrite($this->collection, $this->bulkWrite))
            return true;
        
        return false;
    }

    /**
     * Delete multiple key.
     * 
     * @return bool
     */
    public function deleteMultiple(iterable $keys) 
    {
        foreach ($keys as $key) {
            $this->bulkWrite->delete(['key' => $key]);
        }
        
        if ($this->mongodb->executeBulkWrite($this->collection, $this->bulkWrite))
            return true;

        return false;
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
        if ($this->get($key))
            return true;

        return false;
    }
}