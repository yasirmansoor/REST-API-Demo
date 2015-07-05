<?php

namespace Core;

final class Library {

    private function __construct() {

    }

    public static function instance() {

        static $instance = null;
        if ($instance === null) {
            $instance = new Library();
        }
        return $instance;
    }

    /**
     * @param $data
     */
    function debug($data) {

        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

    /**
     * @param $needle
     * @param $array
     * @return bool
     */
    public function in_assoc($needle, $array) {

        $key = array_keys($array);
        $value = array_values($array);
        if (in_array($needle,$key)){
            return true;
        } elseif (in_array($needle,$value)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $str
     * @return mixed|string
     */
    public function cleanseURI($str) {
        return preg_replace('/[^-a-zA-Z0-9_]/', '',$str);
    }

    /**
     * @param $str
     * @return mixed|string
     */
    public function cleanseInput ($str) {

        $invalid_characters = array("$", "%", "#", "<", ">", "|");
        $str = str_replace($invalid_characters, "", $str);
        $str = @strip_tags($str);
        $str = @stripslashes($str);
        return $str;
    }

    /**
     * @param $string
     * @return bool
     */
    public function isJson($string) {

        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param $path
     * @return bool|array
     */
    public function getDataFromJSONFile($path) {

        //check if it exists and is valid json
        if (file_exists($path)) {

            $data = file_get_contents($path);
            if ($this->isJson($data)) {
                return json_decode($data, true);
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * @param $string
     * @return mixed
     */
    public function convertToNumber($string) {
        return preg_replace("/[^0-9,.]/", "", $string);
    }
} 