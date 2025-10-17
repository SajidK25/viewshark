<?php
/*******************************************************************************************************************
| Software Name        : EasyStream
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) Sami Ahmed
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the EasyStream Proprietary License Agreement.
| 
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2025 Sami Ahmed. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

/**
 * Redis Connection and Management Class
 */
class VRedis
{
    private static $instance = null;
    private $redis = null;
    private $connected = false;
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->connect();
    }
    
    /**
     * Connect to Redis server
     */
    private function connect()
    {
        if (!extension_loaded('redis')) {
            error_log('Redis extension not loaded');
            return false;
        }
        
        try {
            $this->redis = new Redis();
            
            $host = getenv('REDIS_HOST') ?: 'localhost';
            $port = (int)(getenv('REDIS_PORT') ?: 6379);
            $timeout = 5;
            
            $this->connected = $this->redis->connect($host, $port, $timeout);
            
            if ($this->connected) {
                // Set connection options
                $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_JSON);
                $this->redis->setOption(Redis::OPT_PREFIX, 'easystream:');
                
                // Test connection
                $this->redis->ping();
                
                VLogger::getInstance()->info('Redis connected successfully', [
                    'host' => $host,
                    'port' => $port
                ]);
            }
            
        } catch (Exception $e) {
            $this->connected = false;
            error_log('Redis connection failed: ' . $e->getMessage());
            VLogger::getInstance()->error('Redis connection failed', [
                'error' => $e->getMessage(),
                'host' => $host ?? 'unknown',
                'port' => $port ?? 6379
            ]);
        }
        
        return $this->connected;
    }
    
    /**
     * Check if Redis is connected
     */
    public function isConnected()
    {
        return $this->connected && $this->redis !== null;
    }
    
    /**
     * Get Redis instance
     */
    public function getRedis()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        return $this->redis;
    }
    
    /**
     * Set a key-value pair
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$this->isConnected()) {
            return false;
        }
        
        try {
            if ($ttl !== null) {
                return $this->redis->setex($key, $ttl, $value);
            } else {
                return $this->redis->set($key, $value);
            }
        } catch (Exception $e) {
            error_log('Redis SET error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a value by key
     */
    public function get($key)
    {
        if (!$this->isConnected()) {
            return false;
        }
        
        try {
            return $this->redis->get($key);
        } catch (Exception $e) {
            error_log('Redis GET error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a key
     */
    public function delete($key)
    {
        if (!$this->isConnected()) {
            return false;
        }
        
        try {
            return $this->redis->del($key);
        } catch (Exception $e) {
            error_log('Redis DELETE error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if key exists
     */
    public function exists($key)
    {
        if (!$this->isConnected()) {
            return false;
        }
        
        try {
            return $this->redis->exists($key);
        } catch (Exception $e) {
            error_log('Redis EXISTS error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Set expiration for a key
     */
    public function expire($key, $ttl)
    {
        if (!$this->isConnected()) {
            return false;
        }
        
        try {
            return $this->redis->expire($key, $ttl);
        } catch (Exception $e) {
            error_log('Redis EXPIRE error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Push to list (queue)
     */
    public function lpush($key, $value)
    {
        if (!$this->isConnected()) {
            return false;
        }
        
        try {
            return $this->redis->lpush($key, $value);
        } catch (Exception $e) {
            error_log('Redis LPUSH error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Pop from list (queue) - blocking
     */
    public function brpop($keys, $timeout = 0)
    {
        if (!$this->isConnected()) {
            return false;
        }
        
        try {
            return $this->redis->brpop($keys, $timeout);
        } catch (Exception $e) {
            error_log('Redis BRPOP error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get list length
     */
    public function llen($key)
    {
        if (!$this->isConnected()) {
            return 0;
        }
        
        try {
            return $this->redis->llen($key);
        } catch (Exception $e) {
            error_log('Redis LLEN error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Increment counter
     */
    public function incr($key)
    {
        if (!$this->isConnected()) {
            return false;
        }
        
        try {
            return $this->redis->incr($key);
        } catch (Exception $e) {
            error_log('Redis INCR error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Decrement counter
     */
    public function decr($key)
    {
        if (!$this->isConnected()) {
            return false;
        }
        
        try {
            return $this->redis->decr($key);
        } catch (Exception $e) {
            error_log('Redis DECR error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get Redis info
     */
    public function info($section = null)
    {
        if (!$this->isConnected()) {
            return [];
        }
        
        try {
            return $this->redis->info($section);
        } catch (Exception $e) {
            error_log('Redis INFO error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Flush all data (use with caution)
     */
    public function flushAll()
    {
        if (!$this->isConnected()) {
            return false;
        }
        
        try {
            return $this->redis->flushAll();
        } catch (Exception $e) {
            error_log('Redis FLUSHALL error: ' . $e->getMessage());
            return false;
        }
    }
}