<?php

namespace Core;

use Core\Interfaces\ModelInterface;
use Core\Library;

class Model implements ModelInterface {

    public $data = [];
    private $data_path = null;
    private $output_type = 'json';
    private $library = null;
    private $cache = null;
    private $cache_master_key = null;


    public function __construct($path) {

        $this->library = Library::instance();
        $this->cache = Cache::instance();
        $this->populateData($path);
    }

    /**
     * @param string $output_type
     */
    public function setOutputType($output_type) {
        $this->output_type = $output_type;
    }

    /**
     * @return string
     */
    public function getOutputType() {
        return $this->output_type;
    }

    /**
     * @param $path
     * @return bool
     */
    public function populateData($path) {

        //generate cache key
        $this->cache_master_key = $path;
        $key = $this->cache_master_key . ':' . 'all';

        //check if data exists in cache
        $this->data = $this->cache->fetch($key);
        if (! $this->data) {

            //get model data from appropriate json file
            $this->data = [];
            $this->data_path = getcwd() . '/Data/' . $path . '.json';
            $this->data = $this->library->getDataFromJSONFile($this->data_path);

            //add to cache
            $this->cache->add($key, $this->data);
        }

    }

    /**
     * @param $data
     */
    public function renderOutput($data) {

        switch ($this->output_type) {
            case 'xml':
                $xml = new \SimpleXMLElement('<rootTag/>');
                $this->convertToXML($xml, $data);
                return $xml->asXML();
                break;

            case 'json':
            default:
                return $this->convertToJson($data);
                break;
        }
    }

    /**
     * @param $data
     * @return string
     */
    private function convertToJson($data) {
        return json_encode($data);
    }

    /**
     * @param \SimpleXMLElement $object
     * @param array $data
     */
    private function convertToXML(\SimpleXMLElement $object, array $data) {

        foreach ($data as $key => $value) {
            if (is_array($value)) {

                $new_object = $object->addChild($key);
                $this->convertToXML($new_object, $value);

            } else {
                $object->addChild($key, $value);
            }
        }
    }

    /**
     * @return bool
     */
    private function updateDateSource() {

        //delete data cache
        $key = $this->cache_master_key . ':' . 'all';
        $this->cache->delete($key);

        //check if data source still exists
        $data = json_encode($this->data);
        if (file_exists($this->data_path)) {

            //overwrite it
            $filehandle = fopen($this->data_path, 'w');
            fwrite($filehandle, $data);
            fclose($filehandle);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return int
     */
    private function getNewID() {

        if (! empty($this->data)) {
            return end($this->data)['id'] + 1;
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id) {

        unset($this->data[$id]);
        return $this->updateDateSource();
    }

    /**
     * @param array $data
     * @return bool
     */
    public function save(array &$data) {

        if (isset($data['id'])) {
            unset($this->data[$data['id']]);
        } else {
            $data['id'] = $this->getNewID();
        }
        $this->data[$data['id']] = $data;
        return $this->updateDateSource();
    }

    /**
     * @param $key
     * @param $value
     * @param array $data
     * @return array
     */
    private function recursiveFilter($key, $value, array &$data) {

        $result = [];
        foreach($data as $data_item) {
            if ($data_item[$key] == $value) {
                array_push($result, $data_item);
            }
        }
        return $result;
    }

    /**
     * @param array $params
     * @return array
     */
    public function filter (array $params) {

        //generate cache key for this filter
        $key = $this->cache_master_key . ':' . http_build_query($params, '', ',');

        //check if data exists in cache
        $data = $this->cache->fetch($key);
        if (! $data) {

            //get filter data
            $data = $this->data;
            foreach($params as $key => $value) {
                $data = $this->recursiveFilter($key, $value, $data);
            }

            //add to cache
            $this->cache->add($key, $data);

        }
        return $data;
    }


} 