<?php
/**
 * Simple file-based caching system
 */
class Cache {
    private $cache_dir;
    private $cache_time;
    
    /**
     * Constructor
     * 
     * @param string $cache_dir Directory to store cache files
     * @param int $cache_time Cache lifetime in seconds (default: 3600 = 1 hour)
     */
    public function __construct($cache_dir = null, $cache_time = 3600) {
        $this->cache_dir = $cache_dir ?? __DIR__ . '/../cache/';
        $this->cache_time = $cache_time;
        
        // Create cache directory if it doesn't exist
        if (!is_dir($this->cache_dir)) {
            mkdir($this->cache_dir, 0755, true);
        }
    }
    
    /**
     * Get cached content
     * 
     * @param string $key Cache key
     * @return mixed|false Cached content or false if not found/expired
     */
    public function get($key) {
        $filename = $this->getFilename($key);
        
        if (!file_exists($filename)) {
            return false;
        }
        
        // Check if cache is expired
        if (time() - filemtime($filename) > $this->cache_time) {
            unlink($filename);
            return false;
        }
        
        $content = file_get_contents($filename);
        return unserialize($content);
    }
    
    /**
     * Set cache content
     * 
     * @param string $key Cache key
     * @param mixed $content Content to cache
     * @return bool True on success, false on failure
     */
    public function set($key, $content) {
        $filename = $this->getFilename($key);
        $serialized = serialize($content);
        return file_put_contents($filename, $serialized) !== false;
    }
    
    /**
     * Delete cache
     * 
     * @param string $key Cache key
     * @return bool True on success, false on failure
     */
    public function delete($key) {
        $filename = $this->getFilename($key);
        if (file_exists($filename)) {
            return unlink($filename);
        }
        return true;
    }
    
    /**
     * Clear all cache
     * 
     * @return bool True on success, false on failure
     */
    public function clear() {
        $files = glob($this->cache_dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
    }
    
    /**
     * Get cache filename from key
     * 
     * @param string $key Cache key
     * @return string Cache filename
     */
    private function getFilename($key) {
        return $this->cache_dir . md5($key) . '.cache';
    }
} 