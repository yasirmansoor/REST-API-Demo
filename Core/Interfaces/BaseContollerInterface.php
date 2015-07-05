<?php

namespace Core\Interfaces;

interface BaseContollerInterface {

    public function getAllItems(array $params);

    public function getItem(array $params);

    public function setItem(array $params = null);

    public function deleteItem(array $params);

    public function doesItemExist($id);

}