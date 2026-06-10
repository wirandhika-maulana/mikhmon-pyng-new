<?php
/**
 * Mikhmon Local File Cache Layer
 * Minimizes MikroTik API calls to prevent RouterOS log flooding
 */

class MikrotikCache {
    private $cacheDir;
    
    public function __construct() {
        $this->cacheDir = __DIR__ . '/../cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }
    
    private function getFilePath($key, $session) {
        $safeSession = preg_replace('/[^a-zA-Z0-9_]/', '', $session);
        $safeKey = preg_replace('/[^a-zA-Z0-9_]/', '', $key);
        return $this->cacheDir . "/{$safeSession}_{$safeKey}.json";
    }
    
    public function get($key, $session, $ttl_seconds) {
        $file = $this->getFilePath($key, $session);
        if (file_exists($file)) {
            $mtime = filemtime($file);
            if ((time() - $mtime) < $ttl_seconds) {
                $content = file_get_contents($file);
                return json_decode($content, true);
            }
        }
        return false;
    }
    
    public function set($key, $session, $data) {
        $file = $this->getFilePath($key, $session);
        file_put_contents($file, json_encode($data));
        return true;
    }
    
    public function invalidate($key, $session) {
        $file = $this->getFilePath($key, $session);
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    public function invalidatePattern($pattern) {
        $files = glob($this->cacheDir . "/*{$pattern}*.json");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

// Global instance
$mch = new MikrotikCache();

/**
 * Wrapper for $API->comm with caching support
 */
function api_comm_cached($API, $command, $params = [], $cache_key, $session, $ttl = 300) {
    global $mch;
    
    // Disable cache if forced
    if (isset($_GET['nocache'])) {
        $data = $API->comm($command, $params);
        $mch->set($cache_key, $session, $data);
        return $data;
    }
    
    // Try get from cache
    $cached = $mch->get($cache_key, $session, $ttl);
    if ($cached !== false) {
        return $cached;
    }
    
    // Cache miss, call API
    $data = $API->comm($command, $params);
    if (!empty($data) && !isset($data['!trap'])) {
        $mch->set($cache_key, $session, $data);
    }
    
    return $data;
}
