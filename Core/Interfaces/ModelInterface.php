<?php

namespace Core\Interfaces;

interface ModelInterface {

    public function setOutputType($output_type);

    public function getOutputType();

    public function populateData($path);

    public function renderOutput($data);

    public function delete($id);

    public function save(array &$data);


} 