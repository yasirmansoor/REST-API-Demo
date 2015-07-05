<?php

namespace Controller\API;

use Core\Library;
use Core\Interfaces\BaseContollerInterface;
use Core\HTTPResponseHeader;
use Core\Model;

abstract class BaseController implements BaseContollerInterface {

    //used to store type of data item
    protected $type = null;

    //used to store data structure of data item
    protected $columns = array();
    private $library = null;
    private $model = null;

    /**
     * @param $output_type
     */
    public function __construct($output_type) {

        $this->library = Library::instance();

        //get data for object (derived from child class name)
        $this->type = strtolower(end(explode('\\', get_class($this))));
        $this->model = new Model($this->type);
        if (! $this->model->data) {

            $message = $this->type . '  data file could not be found or was empty';
            HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_RESOURCE_NOT_FOUND,
                $message)->quit();
        }
        $this->model->setOutputType($output_type);
    }

    /**
     *
     */
    public function __destruct() {
        $this->model = null;
    }


    /**
     * @param array $params
     * @return mixed|string
     */
    public function getAllItems(array $params = null) {

        if (isset($params)) {

            //filter by optional params (compounded)
            return $this->model->renderOutput($this->model->filter($params));

        } else {

            //show all items
            return $this->model->renderOutput($this->model->data);
        }

    }

    /**
     * @param $id
     * @return mixed|string
     */
    public function getItem(array $params) {

        if (! $this->doesItemExist($params['id'])) {

            $message = $this->type . ' ID ' . $params['id'] . ' does not exist.';
            HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_RESOURCE_NOT_FOUND,
                                                       $message)->quit();
        }
        $params['id'] = $this->library->convertToNumber($params['id']);
        return $this->model->renderOutput($this->model->data[$params['id']]);
    }

    /**
     * @param null $id
     */
    public function setItem(array $params = null) {

        //cleanse POST array first to only accept defined columns
        $data = array_intersect_key($_POST, array_flip($this->columns));
        foreach($data as &$item) {
            $item = $this->library->cleanseInput($item);
        }

        //are we updating or create new item?
        if ($this->isIDPassedIn($params)) {

            //updating an existing item

            $params['id'] = $this->library->convertToNumber($params['id']);

            //did data pass validation
            if (empty($data)) {

                //save existing item has failed
                $message = $this->type . ' ID ' . $params['id'] . ' was not updated, as it failed validation.';
                HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_INTERNAL_SERVER_ERROR,
                    $message)->quit();
            }
            $data['id'] = $params['id'];

        } else {

            //creating a new item

            //did data pass validation
            if (empty($data)) {

                //save new item has failed
                $message = 'New ' . $this->type . ' was not created, as it failed validation.';
                HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_INTERNAL_SERVER_ERROR,
                    $message)->quit();
            }

        }

        //was item saved?
        if ($this->model->save($data)) {

            //save was successful
            if ($this->isIDPassedIn($params)) {
                $message = $this->type . ' ID ' . $params['id'] . ' was saved.';
            } else {
                $message = 'New ' . $this->type . ' item was saved with ID '.$data['id'];
            }
            return $this->model->renderOutput($message);

        } else {

            //save has failed
            if ($this->isIDPassedIn($params)) {
                $message = $this->type . ' ID ' . $params['id'] . ' was not saved';
            } else {
                $message = 'New ' . $this->type . ' item was not saved';
            }

            HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_INTERNAL_SERVER_ERROR,
                $message)->quit();
        };

    }

    /**
     * @param $id
     * @return mixed|string
     */
    public function deleteItem(array $params) {

        if ($this->isIDPassedIn($params)) {

            $message = $this->type . 'ID was not passed in.';
            HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_RESOURCE_NOT_FOUND,
                $message)->quit();
        }

        if (! $this->doesItemExist($params['id'])) {

            $message = $this->type . ' ID ' . $params['id'] . ' does not exist.';
            HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_RESOURCE_NOT_FOUND,
                                                       $message)->quit();
        }

        //delete item from array and update data source
        if ($this->model->delete($params['id'])) {

            //delete was a success
            $message = $this->type . ' ID ' . $params['id'] . ' was deleted.';
            return $this->model->renderOutput($message);

        } else {

            //delete has failed
            $message = $this->type . ' ID ' . $params['id'] . ' was not deleted, even though it exists.';
            HTTPResponseHeader::generateResponseHeader(HTTPResponseHeader::HTTP_INTERNAL_SERVER_ERROR,
                                                       $message)->quit();
        }

    }

    /**
     * @param $id
     * @return bool
     */
    public function doesItemExist($id) {
        return $this->library->in_assoc($id, $this->model->data);
    }

    /**
     * @param null $id
     * @return bool
     */
    private function isIDPassedIn($params = null) {
        return (isset($params['id']) && $params['id']) ? true : false;
    }

}