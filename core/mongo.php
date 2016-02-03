<?php
/**
 *
 * Cellular Framework
 * Mongo 封装
 *
 * @author Cloud 66999882@qq.com
 * @version 1.0 2016-02-03
 *
 */
namespace core;

class Mongo {

    public function __construct($config) {
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
    private function connect() {
      $config = Cellular::loadFile('config/mongo.php');
      $conn = 'mongodb://';
      if (!is_null($config['username']) && !is_null($config['password']) {
        $conn .= $config['username'] . ':' . $config['password'] . '@';
      }
      $conn .= $config['host'] . ':' . $config['port'];
      if (!is_null($config['database'])) {
        $conn .= '/' . $config['database'];
      }
      $mongo = new MongoClient($conn);
  		return $mongo;
    }

}
?>
