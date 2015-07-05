<?php

namespace Core\Interfaces;

interface CacheInterface {

    public function resetAll();

    public function add($key, array $data);

    public function delete($key);

    public function fetch($key);

} 