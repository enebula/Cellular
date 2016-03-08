<?php
/**
 * Cellular Framework
 * 模型基类
 * @copyright Cellular Team
 */
namespace core;

class Model extends DB
{
    /**
     * 根据主键取出一条数据
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

    /**
     * 自增值
     */
    public function increment($field, $num = 1)
    {
        /*
        if (!is_numeric($num)) {
            die('num is not numeric');
        }
        $sql = 'UPDATE `'. $this->table .'` SET `'. $field .'` = `'. $field .'` + '. $num;
        return $this->exec($sql);
        */
    }

    /**
     * 自减值
     */
    public function decrement($field, $num = 1)
    {
        /*
        if (!is_numeric($num)) {
            die('num is not numeric');
        }
        $sql = 'UPDATE `'. $this->table .'` SET `'. $field .'` = `'. $field .'` - '. $num;
        return $this->exec($sql);
        */
    }
}
?>
