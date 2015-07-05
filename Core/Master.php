<?php

namespace Core;

class Master {

    private $uri_parser = null;
    private $cache = null;

    public function __construct() {

        $library = Library::instance();
        $this->cache = Cache::instance();
        $this->uri_parser  =  new URIParser();

        //get router
        $router = new RouterMapper($this->uri_parser->get('controller'));
        if (! $router->exists()) {

            $message = 'No router configuration file found for ' . $this->uri_parser->get('controller');
            HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_RESOURCE_NOT_FOUND,
                $message)->quit();
        }

        //does router require secure connection?
        if ($router->needsHTTPS()) {

            //check if connection is secure
            if (! $this->uri_parser->checkIfSSL()) {

                $message = 'You need to use HTTPS.';
                HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_NO_SSL,
                    $message)->quit();
            }
        }

        //does router require security token
        if ($router->needsSecurityToken()) {
            if (! $this->checkSecurityToken()) {
                return;
            }
        }

        //was a config router called
        if ($this->uri_parser->get('controller') == 'config') {
            $this->configRouter();
            return;
        }

        //use factory to create object and get handle on appropriate controller
        $controller = Factory::create($this->uri_parser->get('controller'), $this->uri_parser->get('format'));

        //get all allowed data params and associated values
        $params = $this->getParams($router->optionalParams());

        //check with router if associated action is allowed
        $method = Factory::getActionVerb($this->uri_parser->get('id'));
        if (! $method) {

            $message = 'No action verb exists for this request';
            HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_RESOURCE_NOT_FOUND,
                $message)->quit();
        }

        //process data and apply associated action
        $data = $controller->$method($params);
        HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_OK, $data, $this->uri_parser->get('format'));
    }

    private function checkSecurityToken() {

        //was a security token and consumer name passed in?
        if (! $this->uri_parser->has('security_token') || $this->uri_parser->get('security_token') == ''
            || ! $this->uri_parser->has('consumer') || $this->uri_parser->get('consumer') == '') {

            $message = 'You need to pass in a security token and consumer.';
            HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_NO_SSL,
                $message)->quit();
        }

        //is it valid?
        $auth = new SecurityToken($this->uri_parser->get('consumer'));
        switch ($auth->authenticate($this->uri_parser->get('security_token'))) {

            case SecurityToken::ERROR_CONSUMER_KEY_UNMATCHED:
                $message = 'Your security token has an unmatched key (consumer does not match)';
                HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_NO_SSL,
                    $message);
                return false;
                break;

            case SecurityToken::ERROR_DATE_EXPIRED:
                $message = 'Your security token has expired';
                HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_NO_SSL,
                    $message);
                return false;
                break;

            case SecurityToken::ERROR_INVALID_FORMAT:
            default:
                $message = 'Your security token was of invalid format';
                HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_NO_SSL,
                    $message);
                return false;
                break;

            case SecurityToken::PASS_OK:
                return true;
                break;
        }
    }

    /**
     * used for admin config
     */
    private function configRouter() {

        switch ($this->uri_parser->getNameByIndex(1)) {
            case 'get_security_token': //generate new security token

                //was a consumer name passed in?
                $auth = new SecurityToken($this->uri_parser->get('get_security_token'));
                if (! $auth->getConsumerKey()) {

                    $message = 'Consumer name was not passed in or did not match.';
                    HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_NO_SSL,
                        $message)->quit();
                }
                $message = 'Key for ' . $this->uri_parser->get('get_security_token') . ' is ' .
                            $auth->encryptWithDate($auth->getConsumerKey());
                HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_OK, $message, $this->uri_parser->get('format'));
                break;

            case 'clear_cache': //clear cache (for debugging only)

                $this->cache->resetAll();
                $message = 'Cache has been cleared';
                HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_OK, $message);
                break;

            case 'get_cache_keys': //get all available cache keys (for debugging only)

                $data = $this->cache->getAllKeys();
                HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_OK, $data);
                break;

            default:
                //do nothing
                break;
        }
    }

    /**
     * @param array $optional_params
     * @return array
     */
    private function getParams(array $optional_params) {

        $params = [];
        if ($this->uri_parser->has('id')) {

            $params['id'] = $this->uri_parser->get('id');
        } else {

            foreach($optional_params as $key) {

                if ($this->uri_parser->has($key)) {
                    $params[$key] = $this->uri_parser->get($key);
                }
            }
        }
        return $params;
    }

}