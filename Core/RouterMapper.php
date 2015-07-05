<?php

namespace Core;

use Core\Library;


class RouterMapper {

    const ROUTER_CONFIG_PATH = '/Config/Router.json';

    private $verbs = [];
    private $optional_params = [];
    private $https = false;
    private $security_token = false;
    private $library = null;
    private $exists = false;


    public function __construct($router_name) {

        $this->library = Library::instance();
        $data = $this->getRouterData();

        if (isset($data[$router_name])) {

            $this->exists = true;
            $this->https = $data[$router_name]['https'];
            $this->security_token = $data[$router_name]['security_token'];
            $this->verbs = explode('|', $data[$router_name]['verbs']);
            if (isset($data[$router_name]['optional_params'])) {
                $this->optional_params = explode('|', $data[$router_name]['optional_params']);
            }
        }

    }

    /**
     * @return boolean
     */
    public function exists(){
        return $this->exists;
    }

    /**
     * @return bool|mixed
     */
    private function getRouterData() {

        //get router data from appropriate json file
        $path = getcwd() . self::ROUTER_CONFIG_PATH;
        return $this->library->getDataFromJSONFile($path);
    }

    /**
     * @return boolean
     */
    public function needsHTTPS() {
        return $this->https;
    }

    /**
     * @return boolean
     */
    public function needsSecurityToken() {
        return $this->security_token;
    }

    /**
     * @param $key
     * @return null
     */
    public function hasVerb($key) {

        if (! isset($this->verbs[$key])) {
            return null;
        }
        return $this->verbs[$key];
    }

    /**
     * @param $key
     * @return null
     */
    public function hasOptionalParam($key) {

        if (! isset($this->optional_params[$key])) {
            return null;
        }
        return $this->optional_params[$key];
    }

    /**
     * @return array
     */
    public function optionalParams() {
        return $this->optional_params;
    }



} 