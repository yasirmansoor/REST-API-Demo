<?php

namespace Core;

use Core\Library;

class URIParser {

    private $uri = [];
    private $library = null;

    /**
     * @param string|null $url
     */
    public function __construct($url = null) {

        $this->library = Library::instance();

        if (! $url) {
            $url= $_SERVER['QUERY_STRING'];
        }

        //parse into key-values pair for each pair of URI params
        $segments = explode('/', $url);
        array_shift($segments);

        $length = count($segments);
        for($i = 0; $i < $length - 1; ++$i) {
            $this->uri[current($segments)] = $this->library->cleanseURI(next($segments), null);
            next($segments);
        }
        unset($segments);
    }

    /**
     * @return array
     */
    public function getURIs() {
        return $this->uri;
    }

    /**
     * @return bool
     */
    public function checkIfSSL() {
        return (empty($_SERVER['HTTPS'])) ? false : false;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key) {

        if (isset($this->uri[$key])) {
            return $this->uri[$key];
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key) {
        return (bool) isset($this->uri[$key]);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function getNameByIndex($index) {

        $keys = array_keys($this->uri);
        return $keys[$index];
    }



}