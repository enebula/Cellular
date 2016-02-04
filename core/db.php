<?php
/**
 * DB - PDO Database Class
 * @author cloud 66999882@qq.com
 * @version 1.0 2016-01-28
 */

namespace core;
use PDO;
use Cellular;

class DB extends Base {

    private $pdo;
    private $prefix;
    private $table;
    private $field;
    private $param; // sql parameter
    private $stmt; // sql statement
    private $where;
    private $whereChild;
    private $order;
    private $limit;
    private $sql;
    protected $query = 0; // 查询次数
    protected $execute = 0; // 执行次数

    /**
     * 构造函数
     */
    function __construct() {
        $this->connect();
    }

    /**
     * 析构函数
     */
    function __destruct() {
        return time();
        //return $this->pdo->query($this->sql);
        $this->pdo = null;
    }

    /**
     * 连接数据库
     */
    private function connect() {
        $config = $this->config('mysql');
        $this->prefix = $config['prefix'];
        $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['database'];
        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password']);
        }
        catch(PDOException $e) {
            die('PDOException: ' . $e->getMessage());
        }
    }

    public function table($param) {
        if (is_null($param)) {
            die('table param is null');
        }
        $this->where = null;
        $this->whereChild = null;
        $this->order = null;
        $this->limit = null;
        $this->table = $param;
        return $this;
    }

    /**
     * 设置WHERE 条件子句
     */
    private function setWhere($value, $type) {
        if (!empty($value)) {
            if (is_array($this->whereChild)) {
                $this->whereChild[][$type] = $value;
            } else {
                $this->where[][$type] = $value;
            }
        }
    }

    private function setChildWhere($type) {
        $this->where[][$type] = $this->whereChild;
        $this->whereChild = null;
    }

    /**
     * 生成WHERE 条件子句
     */
    private function getWhere($value = null) {
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
                $sql.= isset($exp[$key]) ? $exp[$key] : 'AND';
            }
            if (is_array($param)) {
                if (is_array($param[0])) {
                    switch ($key) {
                        case 'and':
                            $sql.= ' (' . $this->getWhere($param) . ') ';
                            break;

                        case 'or':
                            $sql.= ' (' . $this->getWhere($param) . ') ';
                            break;
                    }
                } else {
                    switch ($key) {
                        case 'and':
                            $sql.= ' `' . $param[0] . '` ' . $param[1] . ' \'' . $param[2] . '\' ';
                            break;

                        case 'or':
                            $sql.= ' `' . $param[0] . '` ' . $param[1] . ' \'' . $param[2] . '\' ';
                            break;

                        case 'like':
                            $keyword = $param[2] == 'center' ? '%' . $param[1] . '%' : ($param[2] == 'left' ? '%' . $param[1] : $param[1] . '%');
                            $sql.= ' `' . $param[0] . '` LIKE \'' . $keyword . '\' ';
                            break;

                        case 'notlike':
                            $keyword = $param[2] == 'center' ? '%' . $param[1] . '%' : ($param[2] == 'left' ? '%' . $param[1] : $param[1] . '%');
                            $sql.= ' `' . $param[0] . '` NOT LIKE \'' . $keyword . '\' ';
                            break;

                        case 'between':
                            $sql.= ' `' . $param[0] . '` BETWEEN \'' . $param[1] . '\' AND \'' . $param[2] . '\' ';
                            break;

                        case 'notbetween':
                            $sql.= ' `' . $param[0] . '` NOT BETWEEN \'' . $param[1] . '\' AND \'' . $param[2] . '\' ';
                            break;

                        case 'null':
                            $sql.= ' `' . $param[0] . '` IS NULL ';
                            break;

                        case 'notnull':
                            $sql.= ' `' . $param[0] . '` IS NOT NULL ';
                            break;

                        case 'in':
                            $sql.= ' `' . $param[0] . '` IN(\'' . str_replace(',', '\',\'', $param[1]) . '\')';
                            break;

                        case 'notin':
                            $sql.= ' `' . $param[0] . '` NOT IN(\'' . str_replace(',', '\',\'', $param[1]) . '\')';
                            break;
                    }
                }
            } elseif (is_string($param)) {
                $arr = array(
                    '=' => '` = \'',
                    '>' => '` > \'',
                    '<' => '` < \'',
                    '>=' => '` >= \'',
                    '=<' => '` =< \'',
                    '<>' => '` <> \''
                );
                $sql.= ' `' . strtr($param, $arr) . '\' ';
            }
        }
        return trim($sql);
    }

    /**
     * WHERE 条件子句
     */
    public function where() {
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

    public function orWhere() {
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

    public function like() {
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
        $this->setWhere($value, 'like');
        return $this;
    }

    public function notLike() {
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

    public function between($field, $min, $max) {
        $value = array($field, $min, $max);
        $this->setWhere($value, 'between');
        return $this;
    }

    public function notBetween($field, $min, $max) {
        $value = array($field, $min, $max);
        $this->setWhere($value, 'notbetween');
        return $this;
    }

    public function isNull($field) {
        $value = array($field);
        $this->setWhere($value, 'null');
        return $this;
    }

    public function isNotNull($field) {
        $value = array($field);
        $this->setWhere($value, 'notnull');
        return $this;
    }

    public function in($field, $param) {
        $param = is_array($param) ? implode(',', $param) : $param;
        $value = array($field, $param);
        $this->setWhere($value, 'in');
        return $this;
    }

    public function notIn($field, $param) {
        $param = is_array($param) ? implode(',', $param) : $param;
        $value = array($field, $param);
        $this->setWhere($value, 'notin');
        return $this;
    }

    public function group($param) {
        if (is_null($param)) {
            die('group param is null');
        }
        if (is_array($param)) {
            foreach ($param as $value) {
                $this->group = ',' . $value;
            }
            $this->group = 'GROUP BY ' . substr($this->group, 1);
        }
        if (is_string($param)) {
            $this->group = 'GROUP BY ' . $param;
        }
        return $this;
    }

    public function order($param) {
        if (is_null($param)) {
            die('order param is null');
        }
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $this->order.= ',' . $key . ' ' . $value;
            }
            $this->order = substr($this->order, 1);
        }
        if (is_string($param)) {
            $this->order = $param;
        }
        return $this;
    }

    public function limit($param) {
        if (is_null($param)) {
            die('limit param is null');
        }
        if (is_array($param)) {
            $this->limit = implode(', ', $param);
        } else {
            $this->limit = $param;
        }
        return $this;
    }

    public function query($sql) {
        echo $sql . '<br>';
        if (is_null($this->param)) {
            try {
                $this->stmt = $this->pdo->query($sql, PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                die('PDOException: ' . $e->getMessage());
            }
            return $this->stmt->fetchAll();
        } else {
            $this->stmt = $this->pdo->prepare($sql);
            foreach ($this->param as $key => $value) {
                $this->stmt->bindParam(':' . $key, $value);
            }echo ':' . $this->stmt->debugDumpParams() . '<br>';
            return $this->stmt->execute()->fetchAll();
        }
    }

    /**
     * 查询记录
     */
    public function find($param, $field = 'id', $operator = '=') {
        if (is_null($this->table)) {
            die('table is null');
        }
    }

    /**
     * 查询一条记录
     */
    public function first() {
        // PDO - fetch()
    }

    public function select($param = null) {
        if (is_null($this->table)) {
            die('table is null');
        }
        $sql = 'SELECT ';
        if (is_null($param)) {
            // field is null select all
            $sql.= '*';
        } elseif (is_string($param)) {
            // field is string
            $sql.= $param;
        } elseif (is_array($param)) {
            // field is array
            $sql.= '`' . implode('`,`', $param) . '`';
        }
        $sql.= ' FROM `' . $this->prefix . $this->table . '`';
        if (!is_null($this->where)) {
            $sql.= ' WHERE ' . $this->getWhere();
        }
        if (!is_null($this->order)) {
            $sql.= ' ORDER BY ' . $this->order;
        }
        if (!is_null($this->limit)) {
            $sql.= ' LIMIT ' . $this->limit;
        }
        try {
            return $this->query($sql);
        }
        catch(PDOException $e) {
            die('PDOException: ' . $e->getMessage());
        }
    }

    /**
     * 插入记录
     */
    public function insert($param) {
        if (is_null($this->table)) {
            die('table is null');
        }
        if (is_null($param)) {
            die('insert param is null');
        }
        $key = null;
        $value = null;
        foreach ($param as $k => $v) {
            $key.= ',`' . $k . '`';
            $value.= ',:' . $k;
        }
        $sql = 'INSERT INTO `' . $this->prefix . $this->table . '` (' . substr($key, 1) . ')' . ' VALUES (' . substr($value, 1) . ')';
        echo $sql . '<br>';
        $stmt = $this->pdo->prepare($sql);
        foreach ($param as $k => $v) {
            $stmt->bindParam(':' . $k, $v);
        }
        echo $stmt->debugDumpParams().'<br>';
        return $stmt->execute();
    }

    /**
     * 更新记录
     */
    public function update($param) {
        if (is_null($this->table)) {
            die('table is null');
        }
        if (is_null($param)) {
            die('update param is null');
        }
        $sql = 'UPDATE `' . $this->prefix . $this->table . '` SET ';
        foreach ($param as $k => $v) {
            $sql.= '`' . $k . '`=:' . $k . '';
        }
        if (!is_null($this->where)) {
            $sql.= ' WHERE ' . $this->getWhere();
        }
        if (!is_null($this->order)) {
            $sql.= ' ORDER BY ' . $this->order;
        }
        if (!is_null($this->limit)) {
            $sql.= ' LIMIT ' . $this->limit;
        }
        echo $sql . '<br>';
        $stmt = $this->pdo->prepare($sql);
        foreach ($param as $k => $v) {
            $stmt->bindParam(':' . $k, $v);
        }
        echo $stmt->debugDumpParams().'<br>';
        return $stmt->execute();
    }

    /**
     * 删除记录
     */
    public function delete() {
        if (is_null($this->table)) {
            die('table is null');
        }
        $sql = 'DELETE FROM `' . $this->prefix . $this->table . '`';
        if (!is_null($this->where)) {
            $sql.= ' WHERE ' . $this->getWhere();
        }
        if (!is_null($this->order)) {
            $sql.= ' ORDER BY ' . $this->order;
        }
        if (!is_null($this->limit)) {
            $sql.= ' LIMIT ' . $this->limit;
        }
        echo $sql . '<br>';
        try {
            return $this->pdo->exec($sql);
        }
        catch(PDOException $e) {
            die('PDOException: ' . $e->getMessage());
        }
    }

    /**
     * 清空表
     * 快速清空数据库内指定表内容的 SQL 语句，不保留日志，无法恢复数据，速度也是最快的，比 DELETE 删除方式快非常多。
     */
    public function clear() {
        if (is_null($this->table)) {
            dle('table is null');
        }
        $sql = 'TRUNCATE TABLE `' . $this->prefix . $this->table . '`';
        try {
            return $this->pdo->exec($sql);
        }
        catch(PDOException $e) {
            die('PDOException: ' . $e->getMessage());
        }
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function distinct() {
    }

    /**
     * 绑定参数
     */
    public function bind($param) {
        foreach ($param as $key => $val) {
            $this->param[$key] = $val;
        }
    }

    /**
     * 执行事务
     */
    public function trans() {
        try {
            //$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->beginTransaction();
            $this->exec();
            $this->exec();
            $this->exec();
            $this->pdo->commit();
        }
        catch(Exception $e) {
            $this->pdo->rollBack();
            die('Exception: ' . $e->getMessage());
        }
    }
}
?>
