<?php
/**
 * Cellular Faremwork
 * 模型
 * @copyright Cellular Team
 */

namespace core;


class Model extends DB
{
    public function __construct($table = null)
    {
        parent::__construct();
        if ($table != null) $this->table($table);
        return $this;
    }

    /**
     * 根据主键取出一条数据
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * 行数统计
     */
    public function count($field = null)
    {
        $sql = ($field != null) ? 'COUNT(' . $field . ')' : 'COUNT(*)';
        return $this->column($sql);
    }

    /**
     * 最大值
     */
    public function max($field)
    {
        return $this->column('MAX('. $field .')');
    }

    /**
     * 最小值
     */
    public function min($field)
    {
        return $this->column('MIN('. $field .')');
    }

    /**
     * 平均值
     */
    public function avg($field)
    {
        return $this->column('AVG('. $field .')');
    }

    /**
     * 累加值
     */
    public function sum($field)
    {
        return $this->column('SUM('. $field .')');
    }

    /**
     * 去重查询
     */
    public function distinct($field)
    {
        return $this->column('DISTINCT('. $field .')');
    }
}
?>
