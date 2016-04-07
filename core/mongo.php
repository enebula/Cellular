<?php
/**
 * Cellular Faremwork
 * Mongo 驱动
 * @copyright Cellular Team
 */

namespace core;
use MongoClient;

class Mongo extends Base
{
    public function __construct($config)
    {
        $this->connect();
    }

    /**
     * 连接数据库
     * http://php.net/manual/zh/mongo.connecting.auth.php
     * $m = new MongoClient("mongodb://${username}:${password}@localhost");
     * $m = new MongoClient("mongodb://localhost", array("username" => $username, "password" => $password));
     * $m = new MongoClient("mongodb://${username}:${password}@localhost/myDatabase");
     * $m = new MongoClient("mongodb://${username}:${password}@localhost", array("db" => "myDatabase"));
     */
    private function connect()
    {
        $config = $this->config('mongo');
        $conn = 'mongodb://';
        if (!is_null($config['username']) && !is_null($config['password'])) {
            $conn .= '${' . $config['username'] . '}:${' . $config['password'] . '}@';
        }
        $conn .= $config['host'] . ':' . $config['port'];
        if (!is_null($config['database'])) {
            $conn .= '/' . $config['database'];
        }
        # new MongoClient('mongodb://host:port', ['username'=>$username,'password'=>$password], ['db'=>'$database']);
        $mongo = new \MongoClient($conn);
        return $mongo;
    }
}
?>
