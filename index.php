<?php
use Core\Master;

spl_autoload_register(function($class_name)
{
    $namespace = str_replace("\\","/",__NAMESPACE__);
    $class_name = str_replace("\\","/",$class_name);
    $class = getcwd()."/".(empty($namespace)?"":$namespace."/")."{$class_name}.php";

    if (file_exists($class)) {
        include_once($class);
    } else {

        $message = 'Class ' . $class_name . ' was not found.';
        HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_RESOURCE_NOT_FOUND,
            $message)->quit();
    }

});

$master = new Master();