<?php
/**
 * Cellular Framework
 * 模型基类
 * @copyright Cellular Team
 */
namespace core;

class Model extends DB
{
    public $table;

    /**
     * Model constructor.
     * @param null $table
     */
    public function __construct($table = null)
    {
        if (!is_null($table)) {
            $this->table = $table;
        }
    }

    /**
     * 取出所有记录
     */
    protected function all()
    {
        $sql = 'SELECT * FROM `' . $this->table . '`';
        return $this->query($sql);
    }

    /**
     * 根据主键取出一条数据
     */
    protected function find($id)
    {
        $sql = 'SELECT * FROM `' . $this->table . '` WHERE `id` = \'' . $id . '\'';
        return $this->query($sql);
    }

    /**
     * 行数统计
     */
    protected function count()
    {

        $sql = 'SELECT COUNT(*) FROM `' . $this->table . '`';
        return $this->column($sql);
    }

    /**
     * 最大值
     */
    protected function max($field)
    {
        $sql = 'SELECT MAX(`' . $field . '`) FROM `' . $this->table . '`';
        return $this->column($sql);
    }

    /**
     * 最小值
     */
    protected function min($field)
    {
        $sql = 'SELECT MIN(`' . $field . '`) FROM `' . $this->table . '`';
        return $this->column($sql);
    }

    /**
     * 平均值
     */
    protected function avg($field)
    {
        $sql = 'SELECT AVG(`' . $field . '`) FROM `' . $this->table . '`';
        return $this->column($sql);
    }

    /**
     * 累加值
     */
    protected function sum($field)
    {
        $sql = 'SELECT SUM(`' . $field . '`) FROM `' . $this->table . '`';
        return $this->column($sql);
    }

    /**
     * 自增值
     */
    protected function increment($field, $num = 1)
    {
        if (!is_numeric($num)) {
            die('num is not numeric');
        }
        $sql = 'UPDATE `' . $this->table . '` SET `' . $field . '` = `' . $field . '` + ' . $num;
        return $this->exec($sql);
    }

    /**
     * 自减值
     */
    protected function decrement($field, $num = 1)
    {
        if (!is_numeric($num)) {
            die('num is not numeric');
        }
        $sql = 'UPDATE `' . $this->table . '` SET `' . $field . '` = `' . $field . '` - ' . $num;
        return $this->exec($sql);
    }
}
?>
