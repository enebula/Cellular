<?php
/**
 * Cellular Framework
 * RESTfull API接口控制器
 * @copyright Cellular Team
 */

namespace core;
use Cellular;

class API
{
    protected $model;

    function __construct()
    {
        $this->model = new \stdClass();
    }

    /**
     * 加载模型
     */
    protected function model($name)
    {
        if (isset($this->model->$name)) return $this->model->$name;
        if ($model = Cellular::loadModel($name)) {
            $this->model->$name = $model;
            return $model;
        }
        return false;
    }
}

?>
