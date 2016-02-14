<?php
/**
 * Cellular Framework
 * DB - PDO Database Class
 * @copyright Cellular Team
 */
namespace core;
use PDO;
use Cellular;

class DB extends Base
{
    private $pdo;
    private $prefix;
    private $table;
    private $param; // sql parameter
    private $stmt; // sql statement
    private $where;
    private $whereChild;
    private $group;
    private $order;
    private $limit;
    private $sql;
    protected $query = 0; // 查询次数
    protected $execute = 0; // 执行次数

    /**
     * 构造函数
     */
    function __construct()
    {
        $this->connect();
    }

    /**
     * 析构函数
     */
    function __destruct()
    {
        return time();
        //return $this->pdo->query($this->sql);
        $this->pdo = null;
    }

    /**
     * 连接数据库
     */
    private function connect()
    {
        $config = $this->config('mysql');
        $this->prefix = $config['prefix'];
        $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['database'];
        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password']);
        } catch (PDOException $e) {
            die('PDOException: ' . $e->getMessage());
        }
    }

    /**
     * 格式化字段
     */
    private function formatField($value)
    {
        if (!is_null($value)) {
            if (is_array($value)) $value = implode(',', $value);
            if (is_string($value)) {
                if (strpos($value, '.')) $value = $this->prefix . $value;
                $format = array(
                    ',' => '`,`',
                    '.' => '`.`',
                    '(' => '(`',
                    ')' => '`)',
                );
                return '`' . strtr($value, $format) . '`';
            }
        }
        return null;
    }

    public function table($param)
    {
        if (is_null($param)) {
            die('table param is null');
        }
        $this->param = null;
        $this->join = null;
        $this->where = null;
        $this->whereChild = null;
        $this->group = null;
        $this->order = null;
        $this->limit = null;
        $this->table = $this->prefix . $param;
        return $this;
    }

    public function leftJoin()
    {
        $num = func_num_args();
        $var = func_get_args();
        if ($num > 1) {
            $join = ' LEFT JOIN `' . $this->prefix . $var[0] . '` ON ';
            switch ($num) {
                case 2:
                    $join .= $this->formatField($var[1]);
                    break;
                case 3:
                    $join .= $this->formatField($var[1]) . '=' . $this->formatField($var[2]);
                    break;
                case 4:
                    $var[2] = in_array($var[2], array('=', '>', '<', '>=', '<=', '<>')) ? $var[2] : '=';
                    $join .= $this->formatField($var[1]) . $var[2] . $this->formatField($var[3]);
                    break;
            }
            $this->join[] = $join;
        } else {
            die('leftJoin param is null');
        }
        return $this;
    }

    /**
     * 设置WHERE 条件子句
     */
    private function setWhere($value, $type)
    {
        if (!empty($value)) {
            if (is_array($this->whereChild)) {
                $this->whereChild[][$type] = $value;
            } else {
                $this->where[][$type] = $value;
            }
        }
    }

    private function setChildWhere($type)
    {
        $this->where[][$type] = $this->whereChild;
        $this->whereChild = null;
    }

    /**
     * 生成WHERE 条件子句
     */
    private function getWhere($value = null)
    {
        $sql = '';
        $value = is_null($value) ? $this->where : $value;
        foreach ($value as $k => $val) {
            $key = key($val);
            $param = $val[$key];
            $exp = array(
                'and' => 'AND',
                'or' => 'OR',
            );
            if (0 !== $k) {
                $sql .= isset($exp[$key]) ? $exp[$key] : 'AND';
            }
            if (is_array($param)) {
                if (is_array($param[0])) {
                    switch ($key) {
                        case 'and':
                            $sql .= ' (' . $this->getWhere($param) . ') ';
                            break;

                        case 'or':
                            $sql .= ' (' . $this->getWhere($param) . ') ';
                            break;
                    }
                } else {
                    switch ($key) {
                        case 'and':
                            $sql .= ' `' . $param[0] . '` ' . $param[1] . ' ? ';
                            $this->param[] = $param[2];
                            break;
                        case 'or':
                            $sql .= ' `' . $param[0] . '` ' . $param[1] . ' ? ';
                            $this->param[] = $param[2];
                            break;
                        case 'like':
                            $keyword = ($param[2] == 'both') ? '%?%' : ($param[2] == 'left' ? '%?' : '?%');
                            $sql .= ' `' . $param[0] . '` LIKE ' . $keyword . ' ';
                            $this->param[] = $param[1];
                            break;
                        case 'notlike':
                            $keyword = $param[2] == 'both' ? '%?%' : ($param[2] == 'left' ? '%?' : '?%');
                            $sql .= ' `' . $param[0] . '` NOT LIKE ' . $keyword . ' ';
                            $this->param[] = $param[1];
                            break;
                        case 'between':
                            $sql .= ' `' . $param[0] . '` BETWEEN ? AND ? ';
                            $this->param[] = $param[1];
                            $this->param[] = $param[2];
                            break;
                        case 'notbetween':
                            $sql .= ' `' . $param[0] . '` NOT BETWEEN ? AND ? ';
                            $this->param[] = $param[1];
                            $this->param[] = $param[2];
                            break;
                        case 'null':
                            $sql .= ' `' . $param[0] . '` IS NULL ';
                            break;
                        case 'notnull':
                            $sql .= ' `' . $param[0] . '` IS NOT NULL ';
                            break;
                        case 'in':
                            $param[1] = explode(',', $param[1]);
                            $var = null;
                            foreach ($param[1] as $value) {
                                $this->param[] = $value;
                                $var .= ',?';
                            }
                            $sql .= ' `' . $param[0] . '` IN(' . substr($var, 1) . ') ';
                            break;
                        case 'notin':
                            $param[1] = explode(',', $param[1]);
                            $var = null;
                            foreach ($param[1] as $value) {
                                $this->param[] = $value;
                                $var .= ',?';
                            }
                            $sql .= ' `' . $param[0] . '` NOT IN(' . substr($var, 1) . ') ';
                            break;
                    }
                }
            } elseif (is_string($param)) {
                $arr = array(
                    '=' => '` = \'',
                    '>' => '` > \'',
                    '<' => '` < \'',
                    '>=' => '` >= \'',
                    '<=' => '` <= \'',
                    '<>' => '` <> \''
                );
                $sql .= ' `' . strtr($param, $arr) . '\' ';
            }
        }
        return trim($sql);
    }

    /**
     * WHERE 条件子句
     */
    public function where()
    {
        $num = func_num_args();
        $var = func_get_args();
        if (is_callable($var[0])) {
            $this->whereChild = array(); //'and';
            $var[0]($this);
            $this->setChildWhere('and');
        } else {
            $value = array();
            switch ($num) {
                case 1:
                    //字符串条件
                    $value = $var[0];
                    break;

                case 2:
                    //等于条件
                    $value = array($var[0], '=', $var[1]);
                    break;

                case 3:
                    //其它条件
                    $value = array($var[0], $var[1], $var[2]);
                    break;
            }
            $this->setWhere($value, 'and');
        }
        return $this;
    }

    public function orWhere()
    {
        $num = func_num_args();
        $var = func_get_args();
        if ($num == 0) return $this;
        if (is_callable($var[0])) {
            $this->whereChild = array();
            $var[0]($this);
            $this->setChildWhere('or');
        } else {
            $value = array();
            switch ($num) {
                case 1:
                    //字符串条件
                    $value = $var[0];
                    break;

                case 2:
                    //等于条件
                    $value = array($var[0], '=', $var[1]);
                    break;

                case 3:
                    //其它条件
                    $value = array($var[0], $var[1], $var[2]);
                    break;
            }
            $this->setWhere($value, 'or');
        }
        return $this;
    }

    public function like()
    {
        $num = func_num_args();
        $var = func_get_args();
        $value = array();
        switch ($num) {
            case 1:
                //字符串条件
                $value = $var[0];
                break;

            case 2:
                //全匹配
                $value = array($var[0], $var[1], 'both');
                break;

            case 3:
                //左匹配或右匹配
                $var[2] = $var[2] == 'left' ? 'left' : 'right';
                $value = array($var[0], $var[1], $var[2]);
                break;
        }
        $this->setWhere($value, 'like');
        return $this;
    }

    public function notLike()
    {
        $num = func_num_args();
        $var = func_get_args();
        $value = array();
        switch ($num) {
            case 1:
                //字符串条件
                $value = $var[0];
                break;
            case 2:
                //全匹配
                $value = array($var[0], $var[1], 'center');
                break;
            case 3:
                //左匹配或右匹配
                $var[2] = $var[2] == 'left' ? 'left' : 'right';
                $value = array($var[0], $var[1], $var[2]);
                break;
        }
        $this->setWhere($value, 'notlike');
        return $this;
    }

    public function between($field, $min, $max)
    {
        $value = array($field, $min, $max);
        $this->setWhere($value, 'between');
        return $this;
    }

    public function notBetween($field, $min, $max)
    {
        $value = array($field, $min, $max);
        $this->setWhere($value, 'notbetween');
        return $this;
    }

    public function isNull($field)
    {
        $value = array($field);
        $this->setWhere($value, 'null');
        return $this;
    }

    public function isNotNull($field)
    {
        $value = array($field);
        $this->setWhere($value, 'notnull');
        return $this;
    }

    public function in($field, $param)
    {
        $param = is_array($param) ? implode(',', $param) : $param;
        $value = array($field, $param);
        $this->setWhere($value, 'in');
        return $this;
    }

    public function notIn($field, $param)
    {
        $param = is_array($param) ? implode(',', $param) : $param;
        $value = array($field, $param);
        $this->setWhere($value, 'notin');
        return $this;
    }

    public function group()
    {
        $num = func_num_args();
        $var = func_get_args();
        if ($num < 1) {
            die('group param is null');
        } else {
            $param = ($num > 1) ? implode(',', $var) : $var[0];
            $this->group = $this->formatField($param);
        }
        return $this;
    }

    public function order()
    {
        $num = func_num_args();
        $var = func_get_args();
        if ($num == 1) {
            $this->order[] = $var[0];
        } elseif ($num == 2) {
            $this->order[] = $this->formatField($var[0]) . ' DESC';
        } else {
            die('order param is null');
        }
        return $this;
    }

    public function limit()
    {
        $var = func_get_args();
        $this->limit = null;
        if (isset($var[0]) && is_numeric($var[0])) {
            $this->limit .= $var[0];
        }
        if (isset($var[1]) && is_numeric($var[1])) {
            $this->limit .= ',' . $var[1];
        }
        return $this;
    }

    public function query($sql)
    {
        echo $sql . '<br>';
        if (is_null($this->param)) {
            try {
                $this->stmt = $this->pdo->query($sql, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die('PDOException: ' . $e->getMessage());
            }
            return $this->stmt->fetchAll();
        } else {
            try {
                $this->stmt = $this->pdo->prepare($sql);
                $this->stmt->execute($this->param);
                echo ':' . $this->stmt->debugDumpParams() . '<br>';
            } catch (PDOException $e) {
                die('PDOException: ' . $e->getMessage());
            }
            return $this->stmt->fetchAll();
        }
    }

    /**
     * 查询一条记录
     */
    public function first()
    {
        // PDO - fetch()
    }

    /**
     * 返回受影响的行数
     * @param $sql
     */
    public function exec($sql)
    {
        return $this->pdo->exec($sql);
    }

    /**
     * 查询一条数据中的一列
     */
    public function column($sql)
    {
        echo $sql . '<br>';
        try {
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute();
        } catch (PDOException $e) {
            die('PDOException: ' . $e->getMessage());
        }
        return $this->stmt->fetchColumn();
    }

    public function select($param = null)
    {
        if (is_null($this->table)) {
            die('table is null');
        }
        $param = $this->formatField($param);
        if (is_null($param)) $param = '*';
        $sql = 'SELECT ' . $param;
        $sql .= ' FROM `' . $this->table . '`';
        if (!is_null($this->join)) {
            if (is_array($this->join)) {
                foreach ($this->join as $value) {
                    $sql .= $value;
                }
            }
        }
        if (!is_null($this->where)) {
            $sql .= ' WHERE ' . $this->getWhere();
        }
        if (!is_null($this->group)) {
            $sql .= ' GROUP BY ' . $this->group;
        }
        if (!is_null($this->order)) {
            $sql .= ' ORDER BY ' . implode(',', $this->order);
        }
        if (!is_null($this->limit)) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        try {
            return $this->query($sql);
        } catch (PDOException $e) {
            die('PDOException: ' . $e->getMessage());
        }
    }

    /**
     * 插入记录
     */
    public function insert($param)
    {
        if (is_null($this->table)) {
            die('table is null');
        }
        if (is_null($param)) {
            die('insert param is null');
        }
        $key = null;
        $value = null;
        foreach ($param as $k => $v) {
            $key .= ',`' . $k . '`';
            $value .= ',?';
        }
        $val = array_values($param);
        unset($param);
        $sql = 'INSERT INTO `' . $this->table . '` (' . substr($key, 1) . ')' . ' VALUES (' . substr($value, 1) . ')';
        echo $sql . '<br>';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($val);
    }

    /**
     * 更新记录
     */
    public function update($param)
    {
        if (is_null($this->table)) {
            die('table is null');
        }
        if (is_null($param)) {
            die('update param is null');
        }
        $sql = 'UPDATE `' . $this->table . '` SET ';
        foreach ($param as $k => $v) {
            $sql .= '`' . $k . '`=?';
        }
        $val = array_values($param);
        unset($param);
        if (!is_null($this->where)) {
            $sql .= ' WHERE ' . $this->getWhere();
        }
        if (!is_null($this->group)) {
            $sql .= ' GROUP BY ' . $this->group;
        }
        if (!is_null($this->order)) {
            $sql .= ' ORDER BY ' . implode(',', $this->order);
        }
        if (!is_null($this->limit)) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        echo $sql . '<br>';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($val);
    }

    /**
     * 删除记录
     */
    public function delete()
    {
        if (is_null($this->table)) {
            die('table is null');
        }
        $sql = 'DELETE FROM `' . $this->table . '`';
        if (!is_null($this->where)) {
            $sql .= ' WHERE ' . $this->getWhere();
        }
        if (!is_null($this->group)) {
            $sql .= ' GROUP BY ' . $this->group;
        }
        if (!is_null($this->order)) {
            $sql .= ' ORDER BY ' . implode(',', $this->order);
        }
        if (!is_null($this->limit)) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        echo $sql . '<br>';
        try {
            return $this->pdo->exec($sql);
        } catch (PDOException $e) {
            die('PDOException: ' . $e->getMessage());
        }
    }

    /**
     * 清空表
     * 快速清空数据库内指定表内容的 SQL 语句，不保留日志，无法恢复数据，速度也是最快的，比 DELETE 删除方式快非常多。
     */
    public function clear()
    {
        if (is_null($this->table)) {
            dle('table is null');
        }
        $sql = 'TRUNCATE TABLE `' . $this->table . '`';
        try {
            return $this->pdo->exec($sql);
        } catch (PDOException $e) {
            die('PDOException: ' . $e->getMessage());
        }
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * 去重查询
     */
    public function distinct($param)
    {
        if (is_null($this->table)) {
            dle('table is null');
        }
        if (is_null($param)) {
            dle('param is null');
        }
        $sql = 'SELECT DISTINCT(' . $param . ') FROM `' . $this->table . '`';
        if (!is_null($this->where)) {
            $sql .= ' WHERE ' . $this->getWhere();
        }
        if (!is_null($this->group)) {
            $sql .= ' GROUP BY ' . $this->group;
        }
        if (!is_null($this->order)) {
            $sql .= ' ORDER BY ' . implode(',', $this->order);
        }
        if (!is_null($this->limit)) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        return $this->query($sql);
    }

    /**
     * 绑定参数
     */
    public function bind($param)
    {
        foreach ($param as $key => $val) {
            $this->param[$key] = $val;
        }
    }

    /**
     * 执行事务
     */
    public function trans()
    {
        try {
            //$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->beginTransaction();
            $this->exec();
            $this->exec();
            $this->exec();
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            die('Exception: ' . $e->getMessage());
        }
    }
}
?>
