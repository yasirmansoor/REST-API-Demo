<?php

namespace Core;

class HTTPResponseHeader {

    const HTTP_OK = '200 OK';
    const HTTP_CREATED = '201 Created';
    const HTTP_ACCEPTED = '202 Accepted';
    const HTTP_BAD_REQUEST = '400 Bad Request';
    const HTTP_NO_SSL = '401 Unauthorized';
    const HTTP_RESOURCE_NOT_FOUND = '404 Not Found';
    const HTTP_INTERNAL_SERVER_ERROR = '500 Internal Server Error';

    static private $output_types = array('text' => 'text/html',
                                         'json' => 'application/json',
                                         'xml' => 'application/xml');
    /**
     * @param $http_code
     * @param null $data
     * @param string $output_type
     * @return HTTPResponseHeader
     */
    static public function generateResponseHeader($http_code, $data = null, $output_type = null) {

        header('HTTP/1.1 '. $http_code);
        if (! isset(self::$output_types[$output_type])) {
            $output_type = 'text';
        }
        header('Content-type: ' . self::$output_types[$output_type]);

        if ($data) {
            echo $data;
        }
        return new self();
    }

    static public function quit() {
        exit;
    }


}