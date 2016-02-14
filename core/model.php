<?php
/**
 * Cellular Framework
 * 模型基类
 * @copyright Cellular Team
 */
namespace core;
use Cellular;

class Model extends DB
{
    /**
     * 取出所有记录
     */
    protected function all()
    {

    }

    /**
     * 根据主键取出一条数据
     */
    protected function find($id)
    {

    }

    /**
     * 行数统计
     */
    protected function count()
    {
        if (is_null($this->table)) {
            die('table is null');
        }
        $sql = 'SELECT COUNT(*) FROM `' . $this->table . '`';
        if (!is_null($this->where)) {
            $sql .= ' WHERE ' . $this->getWhere();
        }
        if (!is_null($this->group)) {
            $sql .= ' GROUP BY ' . $this->group;
        }
        return $this->column($sql);
    }

    /**
     * 最大值
     */
    protected function max($field)
    {
        if (is_null($this->table)) {
            die('table is null');
        }
        $sql = 'SELECT MAX(`' . $field . '`) FROM `' . $this->table . '`';
        if (!is_null($this->where)) {
            $sql .= ' WHERE ' . $this->getWhere();
        }
        if (!is_null($this->group)) {
            $sql .= ' GROUP BY ' . $this->group;
        }
        return $this->column($sql);
    }

    /**
     * 最小值
     */
    protected function min($field)
    {
        if (is_null($this->table)) {
            die('table is null');
        }
        $sql = 'SELECT MIN(`' . $field . '`) FROM `' . $this->table . '`';
        if (!is_null($this->where)) {
            $sql .= ' WHERE ' . $this->getWhere();
        }
        if (!is_null($this->group)) {
            $sql .= ' GROUP BY ' . $this->group;
        }
        return $this->column($sql);
    }

    /**
     * 平均值
     */
    protected function avg($field)
    {
        if (is_null($this->table)) {
            die('table is null');
        }
        $sql = 'SELECT AVG(`' . $field . '`) FROM `' . $this->table . '`';
        if (!is_null($this->where)) {
            $sql .= ' WHERE ' . $this->getWhere();
        }
        if (!is_null($this->group)) {
            $sql .= ' GROUP BY ' . $this->group;
        }
        return $this->column($sql);
    }

    /**
     * 累加值
     */
    protected function sum($field)
    {
        if (is_null($this->table)) {
            die('table is null');
        }
        $sql = 'SELECT SUM(`' . $field . '`) FROM `' . $this->table . '`';
        if (!is_null($this->where)) {
            $sql .= ' WHERE ' . $this->getWhere();
        }
        if (!is_null($this->group)) {
            $sql .= ' GROUP BY ' . $this->group;
        }
        return $this->column($sql);
    }

    /**
     * 自增值
     */
    protected function increment($field, $num = null)
    {

    }

    /**
     * 自减值
     */
    protected function decrement($field, $num = null)
    {

    }
}
?>
