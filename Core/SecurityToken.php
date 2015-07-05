<?php

namespace Core;

use Core\Library;

class SecurityToken {

    protected $salt = 'XHSPmuko6wghizF25rmZ';
    protected $consumer_key = null;
    protected $consumer_name = null;

    const MAX_HOURS_BEFORE_EXPIRY = 1;
    const ERROR_INVALID_FORMAT  = 0;
    const ERROR_CONSUMER_KEY_UNMATCHED  = 1;
    const ERROR_DATE_EXPIRED  = 2;
    const PASS_OK = 3;

    /**
     * @param string $consumer_name
     */
    public function __construct($consumer_name) {

        $this->library = Library::instance();
        $this->consumer_name = $consumer_name;
        $this->setConsumerSecurityKey();
    }

    /**
     * @return string
     */
    public function getConsumerKey() {
        return $this->consumer_key;
    }

    /**
     * @return string
     */
    public function getConsumerName() {
        return $this->consumer_name;
    }

    /**
     * @param $string
     * @return mixed|string
     */
    private function safeB64Encode($string) {

        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    /**
     * @param $string
     * @return string
     */
    private function safeB64Decode($string) {

        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    /**
     * @param $value
     * @return bool|string
     */
    public function encrypt($value) {

        if (!$value){
            return false;
        }

        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->salt, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safeB64Encode($crypttext));
    }

    /**
     * @param $value
     * @return bool|string
     */
    public function decrypt($value) {

        if (!$value){
            return false;
        }

        $crypttext = $this->safeB64Decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->salt, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

    /**
     * @param $string
     * @return string
     */
    public function encryptWithDate($string) {

        $string .= '|' . time();
        return $this->encrypt($string);
    }

    /**
     * @param $string
     * @return bool
     */
    public function authenticate($string) {

        $data = $this->decrypt($string);
        $array = explode('|', $data);

        //check correct amount of elements were decrypted
        if (count($array) != 2) {
            return self::ERROR_INVALID_FORMAT;
        }

        //check if consumer key matched
        if ($array[0] != $this->consumer_key) {
            return self::ERROR_CONSUMER_KEY_UNMATCHED;
        }

        //check if it has expired 
        $date = date("Y-m-d H:i:s", $array[1]);
        $start_date  = new \DateTime($date);
        $since_start = $start_date->diff(new \DateTime());
        if ($since_start->h > self::MAX_HOURS_BEFORE_EXPIRY ) {
            return self::ERROR_DATE_EXPIRED;
        }
        return self::PASS_OK;
    }

    /**
     *
     */
    private function setConsumerSecurityKey() {

        //get security key data from appropriate json file
        $path = getcwd() . '/Data/consumer_security_keys.json';
        $data = $this->library->getDataFromJSONFile($path);

        //check if consumer has a key
        if (! isset($data[$this->consumer_name])) {
            $this->consumer_key = null;
        } else {
            $this->consumer_key = $data[$this->consumer_name]['key'];
        }
    }


} 