<?php
/**
 * Cellular Framework
 * Memcached Class
 * @copyright Cellular Team
 */

namespace core;
use Cellular;

class API extends Base
{
    protected $model;

    /**
     * 加载模型
     */
    protected function model($name)
    {
        if (null === $this->model) $this->model = new \stdClass();
        if (isset($this->model->$name)) {
            return $this->model->$name;
        }
        $struct = Cellular::appStruct();
        if ($model = Cellular::loadClass($struct['model'] . '.' . $name)) {
            return $this->model->$name = $model->table($name);
        }
        if ($model = Cellular::loadClass('core.model')) {
            return $this->model->$name = $model->table($name);
        }
        return false;
    }
}

?>
