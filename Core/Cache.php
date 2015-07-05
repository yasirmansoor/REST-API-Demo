<?php

namespace Core;

use Core\Interfaces\CacheInterface;

class Cache implements CacheInterface {

    private $ttl = 120;
    static $instance = null;

    public static function instance() {

        if (self::$instance == null) {
            self::$instance = new Cache();
        }
        return self::$instance;
    }

    public function resetAll() {
        apc_clear_cache();
    }

    /**
     * @param $key
     * @param array $data
     */
    public function add($key, array $data) {
        apc_add($key, $data, $this->ttl);
    }

    /**
     * @param $key
     */
    public function delete($key) {
        apcu_delete($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function fetch($key) {
        return apc_fetch($key);
    }


    /**
     * @param $key
     * @return bool|\string[]
     */
    public function exists($key) {
        return apc_exists($key);
    }

    /**
     * @return array
     */
    public function getAllKeys() {

        $iteration = new \APCIterator('user');
        $data = [];
        foreach ($iteration as $item) {
            array_push($data , $item['key']);
        }
        return $data;
    }




} 