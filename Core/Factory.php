<?php

namespace Core;

class Factory {

    /**
     * @param $class_name
     * @param $output_type
     * @return mixed Controller\API\BaseController
     */
    public static function create($class_name, $output_type) {

        $class_name = 'Controller\\API\\' . ucfirst($class_name);
        if(class_exists($class_name)) {

            return new $class_name($output_type);
        } else {
            $message = 'Object ' . $class_name . ' was not found.';
            HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_RESOURCE_NOT_FOUND,
                                                       $message)->quit();
        }
    }


    /**
     * map verb to expected method
     *
     * @param null $param
     * @return null|string
     */
    static public function getActionVerb($param = null) {

        $method = null;

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':

                //if no ID was passed in, then show all items
                if ($param) {
                    $method = 'getItem';
                } else {
                    $method = 'getAllItems';
                }
                break;

            case 'PUT':
                if ($param) {
                    $method = 'setItem';
                }
                break;

            case 'POST':
                $method = 'setItem';
                break;

            case 'DELETE':
                $method = 'deleteItem';

            default:
                break;
        }
        return $method;
    }
}