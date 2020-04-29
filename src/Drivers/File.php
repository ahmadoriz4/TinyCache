<?php

/**
 * Simple Driver for File.
 *
 * This class just wrap the File PHP Ext and follow PSR 16 Interface.
 * We don't extend File in this class. Just inject the class in construct.
 * Maybe in the future we change `php-File` to other Ext.
 * 
 * @package Drivers
 * @author Yllumi
 */

namespace Gemblue\TinyCache\Drivers;

use Gemblue\TinyCache\Interfaces\CacheInterface;

class File implements CacheInterface
{
    /** Ext container */
    protected $file_path;
    
    /**
     * Constructor 
     * 
     * Handle connection and inject ext.
     * 
     * @return void
     */
    public function __construct(array $options)
    {       
        if (! ($options['file_path'] ?? false)) {
            return new \Exception('File cache configuration not set: file_path');
        }

        $this->file_path = $options['file_path'];
        
        if (!file_exists($this->file_path)) {
            return new \Exception('Cache folder not found: '.$this->file_path);
        }
    }
    
    /**
     * Get key with default if not exist.
     * 
     * @return mixed
     */
    public function get(string $key, $default = null) 
    {
        if ( ! is_file($this->file_path.$key))
        {
            return FALSE;
        }

        $data = unserialize(file_get_contents($this->file_path.$key));

        if ($data['ttl'] > 0 && time() > $data['time'] + $data['ttl'])
        {
            file_exists($this->file_path.$key) && unlink($this->file_path.$key);
            return FALSE;
        }

        return is_array($data) ? $data['data'] : $default ?? false;
    }

    /**
     * Set key, value, expire.
     * 
     * @return bool
     */
    public function set(string $key, $value, ?int $ttl = 300) 
    {
        $contents = array(
            'time'      => time(),
            'ttl'       => $ttl,
            'data'      => $value
        );

        if ($this->write_file($this->file_path.$key, serialize($contents)))
        {
            chmod($this->file_path.$key, 0640);
            return TRUE;
        }

        return FALSE;
    }
    
    /**
     * Delete key
     * 
     * @return bool
     */
    public function delete(string $key) 
    {
        $key = $this->file_path.$key;

        if(strpos($key, '*'))
            return array_map('unlink', glob($key));

        return is_file($key) ? unlink($key) : FALSE;
    }

    /**
     * To Wipe Cache.
     * 
     * @return bool
     */
    public function clear() 
    {
        return $this->delete_files($this->file_path, FALSE, TRUE);
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
            if (!$this->set($key, $value, $ttl))
                return false;
        }

        return true;
    }

    /**
     * Delete multiple key.
     * 
     * @return bool
     */
    public function deleteMultiple(array $keys) 
    {
        foreach ($keys as $key) {
            if (!$this->delete($key))
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
        if ($this->get($key))
            return true;

        return false;
    }


    /**
     * Write File
     *
     * Writes data to the file specified in the path.
     * Creates a new file if non-existent.
     *
     * @param   string  $path   File path
     * @param   string  $data   Data to write
     * @param   string  $mode   fopen() mode (default: 'wb')
     * @return  bool
     */
    private function write_file($path, $data, $mode = 'wb')
    {
        if ( ! $fp = @fopen($path, $mode))
        {
            return FALSE;
        }

        flock($fp, LOCK_EX);

        for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
        {
            if (($result = fwrite($fp, substr($data, $written))) === FALSE)
            {
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return is_int($result);
    }

    /**
     * Delete Files
     *
     * Deletes all files contained in the supplied directory path.
     * Files must be writable or owned by the system in order to be deleted.
     * If the second parameter is set to TRUE, any directories contained
     * within the supplied base directory will be nuked as well.
     *
     * @param   string  $path       File path
     * @param   bool    $del_dir    Whether to delete any directories found in the path
     * @param   bool    $htdocs     Whether to skip deleting .htaccess and index page files
     * @param   int $_level     Current directory depth level (default: 0; internal use only)
     * @return  bool
     */
    private function delete_files($path, $del_dir = FALSE, $htdocs = FALSE, $_level = 0)
    {
        // Trim the trailing slash
        $path = rtrim($path, '/\\');

        if ( ! $current_dir = @opendir($path))
        {
            return FALSE;
        }

        while (FALSE !== ($filename = @readdir($current_dir)))
        {
            if ($filename !== '.' && $filename !== '..')
            {
                $filepath = $path.DIRECTORY_SEPARATOR.$filename;

                if (is_dir($filepath) && $filename[0] !== '.' && ! is_link($filepath))
                {
                    $this->delete_files($filepath, $del_dir, $htdocs, $_level + 1);
                }
                elseif ($htdocs !== TRUE OR ! preg_match('/^(\.htaccess|index\.(html|htm|php)|web\.config)$/i', $filename))
                {
                    @unlink($filepath);
                }
            }
        }

        closedir($current_dir);

        return ($del_dir === TRUE && $_level > 0)
            ? @rmdir($path)
            : TRUE;
    }
}