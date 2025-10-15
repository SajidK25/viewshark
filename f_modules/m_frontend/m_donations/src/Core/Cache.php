<?php
namespace Donations\Core;

class Cache {
    private static $instance = null;
    private $config;
    private $cache;

    private function __construct() {
        $this->config = require DONATIONS_PATH . '/config/config.php';
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        try {
            $this->cache = new \Memcached();
            $this->cache->addServer(
                $this->config['cache']['host'],
                $this->config['cache']['port']
            );
        } catch (\Exception $e) {
            throw new \Exception("Cache connection failed: " . $e->getMessage());
        }
    }

    public function get($key) {
        $value = $this->cache->get($key);
        return $value !== false ? $value : null;
    }

    public function set($key, $value, $ttl = 3600) {
        return $this->cache->set($key, $value, time() + $ttl);
    }

    public function delete($key) {
        return $this->cache->delete($key);
    }

    public function increment($key, $value = 1) {
        return $this->cache->increment($key, $value);
    }

    public function decrement($key, $value = 1) {
        return $this->cache->decrement($key, $value);
    }

    public function flush() {
        return $this->cache->flush();
    }

    public function getMulti($keys) {
        return $this->cache->getMulti($keys);
    }

    public function setMulti($items, $ttl = 3600) {
        return $this->cache->setMulti($items, time() + $ttl);
    }

    public function deleteMulti($keys) {
        return $this->cache->deleteMulti($keys);
    }

    public function remember($key, $ttl, $callback) {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }

    public function rememberForever($key, $callback) {
        return $this->remember($key, 0, $callback);
    }

    public function has($key) {
        return $this->get($key) !== null;
    }

    public function missing($key) {
        return !$this->has($key);
    }

    public function pull($key) {
        $value = $this->get($key);
        $this->delete($key);
        return $value;
    }

    public function put($key, $value, $ttl = 3600) {
        return $this->set($key, $value, $ttl);
    }

    public function add($key, $value, $ttl = 3600) {
        return $this->cache->add($key, $value, time() + $ttl);
    }

    public function forever($key, $value) {
        return $this->set($key, $value, 0);
    }

    public function forget($key) {
        return $this->delete($key);
    }

    public function tags($names) {
        return new TaggedCache($this, (array) $names);
    }

    public function flushTags($names) {
        foreach ((array) $names as $name) {
            $this->delete("tag:{$name}:keys");
        }
    }

    public function getConnection() {
        return $this->cache;
    }

    public function __destruct() {
        $this->cache = null;
    }

    private function __clone() {}
    private function __wakeup() {}
}

class TaggedCache {
    private $cache;
    private $names;

    public function __construct($cache, $names) {
        $this->cache = $cache;
        $this->names = $names;
    }

    public function get($key) {
        return $this->cache->get($this->taggedItemKey($key));
    }

    public function put($key, $value, $ttl = 3600) {
        $this->cache->set($this->taggedItemKey($key), $value, $ttl);
        $this->pushKey($key);
    }

    public function remember($key, $ttl, $callback) {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->put($key, $value, $ttl);
        
        return $value;
    }

    public function rememberForever($key, $callback) {
        return $this->remember($key, 0, $callback);
    }

    public function forget($key) {
        $this->cache->delete($this->taggedItemKey($key));
        $this->pullKey($key);
    }

    public function flush() {
        foreach ($this->names as $name) {
            $this->cache->flushTags($name);
        }
    }

    private function taggedItemKey($key) {
        return implode(':', array_merge($this->names, [$key]));
    }

    private function pushKey($key) {
        foreach ($this->names as $name) {
            $keys = $this->cache->get("tag:{$name}:keys") ?: [];
            if (!in_array($key, $keys)) {
                $keys[] = $key;
                $this->cache->forever("tag:{$name}:keys", $keys);
            }
        }
    }

    private function pullKey($key) {
        foreach ($this->names as $name) {
            $keys = $this->cache->get("tag:{$name}:keys") ?: [];
            if (($index = array_search($key, $keys)) !== false) {
                unset($keys[$index]);
                $this->cache->forever("tag:{$name}:keys", array_values($keys));
            }
        }
    }
} 